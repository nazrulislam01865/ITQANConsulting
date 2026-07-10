const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
global.window = {};
const engineSource = fs.readFileSync(path.join(__dirname, '../../public/assets/itqan-external-guest-map/graph-walker-engine.js'), 'utf8');
vm.runInThisContext(engineSource, {filename: 'graph-walker-engine.js'});
const GraphWalker = global.window.ResortGraphWalker;

const seed = JSON.parse(fs.readFileSync(path.join(__dirname, '../../public/assets/itqan-external-guest-map/seed/palace-path-map-data.json'), 'utf8'));
const nodes = seed.nodes;
const edges = seed.edges.map((edge, index) => ({
  id: index + 1,
  from: edge.a,
  to: edge.b,
  path: edge.points.map(([x, y]) => ({x, y})),
}));
const metersPerPixel = Number(seed.settings.metersPerPixel);

function makeWalker() {
  return new GraphWalker({
    nodes,
    edges,
    metersPerPixel,
    routeBiasDegrees: 9,
    branchLockMeters: 3.2,
    reversePenaltyDegrees: 44,
    reverseAllowanceDegrees: 38,
    reverseSwitchMarginDegrees: 30,
  });
}

// Every mapped junction must select every connected branch when the phone
// heading points along that branch.
for (const nodeCode of Object.keys(nodes)) {
  const probe = makeWalker();
  probe.setPosition(nodes[nodeCode], 0, nodeCode);
  const candidates = probe.adj[nodeCode] || [];
  for (const candidate of candidates) {
    const walker = makeWalker();
    walker.setPosition(nodes[nodeCode], candidate.bearing, nodeCode);
    const result = walker.step(0.8, candidate.bearing, null);
    assert(result.selectedBranch, `${nodeCode}: no branch selected for heading ${candidate.bearing}`);
    assert.strictEqual(String(result.selectedBranch.edgeId), String(candidate.edgeId), `${nodeCode}: wrong edge for ${candidate.toNode}`);
    assert.strictEqual(result.selectedBranch.toNode, candidate.toNode, `${nodeCode}: wrong destination branch`);
    assert(result.traveledMeters > 0, `${nodeCode}: no movement on selected branch`);
  }
}


// Small compass noise must not change the intended branch.
for (const nodeCode of Object.keys(nodes)) {
  const probe = makeWalker();
  probe.setPosition(nodes[nodeCode], 0, nodeCode);
  for (const candidate of probe.adj[nodeCode] || []) {
    for (const delta of [-7, 7]) {
      const walker = makeWalker();
      walker.setPosition(nodes[nodeCode], candidate.bearing + delta, nodeCode);
      const result = walker.step(0.8, candidate.bearing + delta, null);
      assert.strictEqual(result.selectedBranch.toNode, candidate.toNode, `${nodeCode}: ${delta}° sensor noise selected the wrong road`);
    }
  }
}

// After arriving from every incident road, every outgoing road—including a
// deliberate U-turn—must still be selectable by heading.
for (const nodeCode of Object.keys(nodes)) {
  const template = makeWalker();
  const outgoing = template.adj[nodeCode] || [];
  for (const incomingAtNode of outgoing) {
    const neighborCode = incomingAtNode.toNode;
    const inboundTemplate = template.adj[neighborCode].find(item => item.toNode === nodeCode && item.edgeId === incomingAtNode.edgeId);
    assert(inboundTemplate, `${neighborCode} -> ${nodeCode}: inbound edge missing`);
    for (const desired of outgoing) {
      const walker = makeWalker();
      walker.setPosition(nodes[neighborCode], inboundTemplate.bearing, neighborCode);
      const arrived = walker.step(inboundTemplate.edge.lengthPx * metersPerPixel, inboundTemplate.bearing, inboundTemplate.edgeId);
      assert.strictEqual(arrived.kind, 'node', `${neighborCode} -> ${nodeCode}: did not reach junction`);
      assert.strictEqual(arrived.nodeCode, nodeCode, `${neighborCode} -> ${nodeCode}: reached wrong node`);
      const turn = walker.step(0.8, desired.bearing, null);
      assert(turn.selectedBranch, `${nodeCode}: no outgoing branch after arriving from ${neighborCode}`);
      assert.strictEqual(turn.selectedBranch.toNode, desired.toNode, `${nodeCode}: could not choose ${desired.toNode} after arriving from ${neighborCode}`);
    }
  }
}

// Reproduce the actual Kids Zone -> Lake North junction from the recording.
const fromKids = makeWalker();
fromKids.setPosition({x: 652, y: 294}, 84, 'n_kids');
const kidsToLake = fromKids.adj.n_kids.find(item => item.toNode === 'n_lake_n');
assert(kidsToLake, 'Kids -> Lake North edge missing');
let result = fromKids.step(kidsToLake.edge.lengthPx * metersPerPixel, kidsToLake.bearing, kidsToLake.edgeId);
assert.strictEqual(result.kind, 'node');
assert.strictEqual(result.nodeCode, 'n_lake_n');

const lakeBranches = fromKids.adj.n_lake_n;
for (const target of ['n_resto', 'n_loop', 'n_kids']) {
  const walker = makeWalker();
  walker.setPosition({x: 652, y: 294}, kidsToLake.bearing, 'n_kids');
  walker.step(kidsToLake.edge.lengthPx * metersPerPixel, kidsToLake.bearing, kidsToLake.edgeId);
  const candidate = lakeBranches.find(item => item.toNode === target);
  assert(candidate, `Lake junction branch ${target} missing`);
  const turn = walker.step(0.9, candidate.bearing, null);
  assert(turn.selectedBranch, `Lake junction did not select ${target}`);
  assert.strictEqual(turn.selectedBranch.toNode, target, `Lake junction selected wrong branch instead of ${target}`);
}


// Complete the recorded Kids Zone -> Family Pool journey through both valid
// junction choices to prove multi-junction continuity.
function walkWholeEdge(walker, fromNode, toNode) {
  const candidate = walker.adj[fromNode].find(item => item.toNode === toNode);
  assert(candidate, `${fromNode} -> ${toNode}: edge missing`);
  const result = walker.step(candidate.edge.lengthPx * metersPerPixel, candidate.bearing, candidate.edgeId);
  assert.strictEqual(result.kind, 'node', `${fromNode} -> ${toNode}: did not finish on a node`);
  assert.strictEqual(result.nodeCode, toNode, `${fromNode} -> ${toNode}: wrong node`);
  return result;
}

const directPoolWalker = makeWalker();
directPoolWalker.setPosition(nodes.n_kids, kidsToLake.bearing, 'n_kids');
walkWholeEdge(directPoolWalker, 'n_kids', 'n_lake_n');
walkWholeEdge(directPoolWalker, 'n_lake_n', 'n_resto');
walkWholeEdge(directPoolWalker, 'n_resto', 'n_pool');

const loopPoolWalker = makeWalker();
loopPoolWalker.setPosition(nodes.n_kids, kidsToLake.bearing, 'n_kids');
walkWholeEdge(loopPoolWalker, 'n_kids', 'n_lake_n');
walkWholeEdge(loopPoolWalker, 'n_lake_n', 'n_loop');
walkWholeEdge(loopPoolWalker, 'n_loop', 'n_pool');

// A user who turns around in the middle of a road must move back on the same road.
const reverseWalker = makeWalker();
reverseWalker.setPosition(nodes.n_kids, kidsToLake.bearing, 'n_kids');
reverseWalker.step(8, kidsToLake.bearing, kidsToLake.edgeId);
const beforeReverse = reverseWalker.snapshot();
const reverseHeading = GraphWalker.normalizeDegrees(kidsToLake.bearing + 180);
const reversed = reverseWalker.step(1, reverseHeading, null);
assert.strictEqual(reversed.edgeId, beforeReverse.edgeId);
assert.strictEqual(reversed.direction, -beforeReverse.direction, 'walker did not reverse after a clear U-turn');

console.log(`Graph walker tests passed for ${Object.keys(nodes).length} nodes and ${edges.length} edges.`);
