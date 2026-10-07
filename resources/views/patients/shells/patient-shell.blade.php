@extends('layouts.app')

@section('content')
@php
    $initialTab = match (true) {
        request()->routeIs('patients.appointments') => 'appointments',
        request()->routeIs('patients.health.records') => 'health-records',
        request()->routeIs('patients.profile') => 'profile',
        request()->routeIs('patients.certificates.index') => 'certificates',
        default => 'dashboard',
    };
@endphp

<div>
    <div id="tab-content" aria-live="polite">
        <div class="flex items-center justify-center py-12" role="status">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-slate-200 border-b-teal-600"></div>
            <span class="sr-only">Loading section</span>
        </div>
    </div>
</div>

<script>
    const patientTabUrls = Object.freeze({
        dashboard: @json(route('patients.ajax.dashboard')),
        appointments: @json(route('patients.ajax.appointments')),
        'health-records': @json(route('patients.ajax.health-records')),
        profile: @json(route('patients.ajax.profile')),
        certificates: @json(route('patients.ajax.certificates')),
    });

    const patientPageUrls = Object.freeze({
        dashboard: @json(route('patients.dashboard')),
        appointments: @json(route('patients.appointments')),
        'health-records': @json(route('patients.health.records')),
        profile: @json(route('patients.profile')),
        certificates: @json(route('patients.certificates.index')),
    });

    const initialPatientTab = @json($initialTab);

    function renderPatientTabError(message, tabName) {
        const contentDiv = document.getElementById('tab-content');
        contentDiv.innerHTML = `
            <div class="rounded-xl border border-red-300 bg-red-50 px-4 py-4 text-red-700">
                <p class="font-bold">Error loading content</p>
                <p class="mt-1">${message}</p>
                <button type="button"
                        class="mt-3 rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800"
                        onclick="switchTab('${tabName}', false)">
                    Try again
                </button>
            </div>
        `;
    }

    // Load a patient section using Laravel-generated URLs. This also works when
    // the application is installed in a subdirectory instead of the domain root.
    function switchTab(tabName, updateHistory = true) {
        const ajaxUrl = patientTabUrls[tabName];
        const pageUrl = patientPageUrls[tabName];

        if (!ajaxUrl || !pageUrl) {
            renderPatientTabError('The requested section is unavailable.', 'dashboard');
            return;
        }

        if (updateHistory) {
            history.pushState({ tab: tabName }, '', pageUrl);
        }

        // Show loading indicator
        const contentDiv = document.getElementById('tab-content');
        contentDiv.innerHTML = `
            <div class="flex items-center justify-center py-12" role="status">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-slate-200 border-b-teal-600"></div>
                <span class="sr-only">Loading section</span>
            </div>
        `;

        // Load content via AJAX
        fetch(ajaxUrl, {
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        })
            .then(response => {
                if (response.status === 423) { window.location.reload(); throw new Error('Session locked'); }
                if (!response.ok) throw new Error(`Unable to load this section (${response.status})`);
                return response.text();
            })
            .then(html => {
                contentDiv.innerHTML = html;
                initTabScripts(tabName);
            })
            .catch(error => {
                if (error.message !== 'Session locked') {
                    renderPatientTabError(error.message, tabName);
                }
            });
    }

    function initTabScripts(tabName) {
        // Add any tab-specific script initialization here
    }

    // Handle browser back/forward
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.tab) {
            switchTab(e.state.tab, false);
            return;
        }

        window.location.reload();
    });

    // Load the section represented by the current named Laravel route. Avoid
    // parsing the URL with \w because valid slugs such as "health-records"
    // contain a hyphen and were previously shortened to "health", causing 404s.
    document.addEventListener('DOMContentLoaded', function() {
        history.replaceState({ tab: initialPatientTab }, '', patientPageUrls[initialPatientTab]);
        switchTab(initialPatientTab, false);
    });
</script>
@endsection
