@php
  $orderErrors = $errors->getBag('workOrder');
  $initialWorkKey = old('selected_work_key', '');
  $initialWork = collect($collections['works'] ?? [])->first(
      fn (array $work): bool => ($work['order_key'] ?? '') === $initialWorkKey
  );
  $budgetOptions = $collections['contact_options']['budgets'] ?? [
      'Not decided yet',
      'Under BDT 100,000',
      'BDT 100,000 – 300,000',
      'BDT 300,000 – 800,000',
      'Above BDT 800,000',
  ];
@endphp

<div
  class="work-order-modal"
  data-work-order-modal
  data-open-on-load="{{ $orderErrors->any() ? 'true' : 'false' }}"
  data-initial-work-key="{{ $initialWorkKey }}"
  data-initial-work-title="{{ $initialWork['title'] ?? '' }}"
  hidden
>
  <div class="work-order-backdrop" data-work-order-close></div>
  <section class="work-order-dialog" role="dialog" aria-modal="true" aria-labelledby="workOrderTitle" tabindex="-1">
    <button class="work-order-close" type="button" data-work-order-close aria-label="Close order form">×</button>

    <div class="work-order-heading">
      <span class="label">Order a similar solution</span>
      <h2 id="workOrderTitle">Tell us what you need.</h2>
      <p>Use this work as a starting point. ITQAN will review your requirements and contact you to clarify scope, timeline, and cost.</p>
    </div>

    @if($orderErrors->any())
      <div class="form-status error" role="alert">
        Please correct the highlighted information.
        <ul>
          @foreach($orderErrors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form class="work-order-form" method="POST" action="{{ route('work-orders.store') }}" data-work-order-form>
      @csrf
      <input type="hidden" name="selected_work_key" value="{{ $initialWorkKey }}" data-work-order-key>
      <div class="work-order-honeypot" aria-hidden="true">
        <label for="orderWebsite">Website</label>
        <input id="orderWebsite" type="text" name="website" value="" tabindex="-1" autocomplete="off">
      </div>

      <div class="work-order-selected">
        <span>Selected work</span>
        <strong data-work-order-title>{{ $initialWork['title'] ?? 'Choose a work item' }}</strong>
      </div>

      <div class="work-order-fields">
        <div class="field">
          <label for="orderCustomerName">Your name <span aria-hidden="true">*</span></label>
          <input id="orderCustomerName" name="customer_name" type="text" value="{{ old('customer_name') }}" maxlength="160" autocomplete="name" required>
        </div>

        <div class="field">
          <label for="orderCompanyName">Company / organization</label>
          <input id="orderCompanyName" name="company_name" type="text" value="{{ old('company_name') }}" maxlength="180" autocomplete="organization">
        </div>

        <div class="field">
          <label for="orderEmail">Email <span aria-hidden="true">*</span></label>
          <input id="orderEmail" name="email" type="email" value="{{ old('email') }}" maxlength="190" autocomplete="email" required>
        </div>

        <div class="field">
          <label for="orderPhone">Phone / WhatsApp <span aria-hidden="true">*</span></label>
          <input id="orderPhone" name="phone" type="tel" value="{{ old('phone') }}" maxlength="80" autocomplete="tel" required>
        </div>

        <div class="field">
          <label for="orderContactMethod">Preferred contact method <span aria-hidden="true">*</span></label>
          <select id="orderContactMethod" name="preferred_contact_method" required>
            <option value="">Select one</option>
            <option value="email" @selected(old('preferred_contact_method') === 'email')>Email</option>
            <option value="phone" @selected(old('preferred_contact_method') === 'phone')>Phone call</option>
            <option value="whatsapp" @selected(old('preferred_contact_method') === 'whatsapp')>WhatsApp</option>
          </select>
        </div>

        <div class="field">
          <label for="orderBudget">Estimated budget <span aria-hidden="true">*</span></label>
          <select id="orderBudget" name="budget_range" required>
            <option value="">Select a range</option>
            @foreach($budgetOptions as $budget)
              <option value="{{ $budget }}" @selected(old('budget_range') === $budget)>{{ $budget }}</option>
            @endforeach
          </select>
        </div>

        <div class="field full">
          <label for="orderTimeline">Preferred timeline <span aria-hidden="true">*</span></label>
          <select id="orderTimeline" name="timeline" required>
            <option value="">Select a timeline</option>
            @foreach(['As soon as possible', 'Within 1 month', 'Within 1–3 months', 'Within 3–6 months', 'Flexible / not decided'] as $timeline)
              <option value="{{ $timeline }}" @selected(old('timeline') === $timeline)>{{ $timeline }}</option>
            @endforeach
          </select>
        </div>

        <div class="field full">
          <label for="orderProjectSummary">What do you want to build or improve? <span aria-hidden="true">*</span></label>
          <textarea id="orderProjectSummary" name="project_summary" minlength="20" maxlength="3000" placeholder="Describe the business problem, users, and outcome you expect." required>{{ old('project_summary') }}</textarea>
          <small>Minimum 20 characters.</small>
        </div>

        <div class="field full">
          <label for="orderRequirements">Important features or requirements</label>
          <textarea id="orderRequirements" name="requirements" maxlength="5000" placeholder="List any must-have features, integrations, reports, platforms, or constraints.">{{ old('requirements') }}</textarea>
        </div>
      </div>

      <label class="work-order-consent">
        <input type="checkbox" name="consent" value="1" @checked(old('consent')) required>
        <span>I agree that ITQAN Consulting may contact me about this order request.</span>
      </label>

      <div class="work-order-actions">
        <button class="btn ghost-light" type="button" data-work-order-close>Cancel</button>
        <button class="btn blue" type="submit">Submit Order Request</button>
      </div>
    </form>
  </section>
</div>
