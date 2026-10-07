@extends('layouts.app')

@section('content')
<div>
    <div id="tab-content" aria-live="polite">
        <!-- Content will be loaded dynamically -->
    </div>
</div>

<script>
    // Update navigation highlight based on current URL
    function updateNavHighlight() {
        const path = window.location.pathname;
        const navLinks = document.querySelectorAll('.clinic-nav-link[href*="/staff/"]');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            const linkPath = href.replace(window.location.origin, '');

            if (path === linkPath || path === linkPath.replace(/\/$/, '')) {
                link.classList.add('clinic-nav-link-active');
            } else {
                link.classList.remove('clinic-nav-link-active');
            }
        });
    }

    // Tab switching functionality
    function switchTab(tabName) {
        // Update URL without page reload
        const url = `/staff/${tabName === 'dashboard' ? '' : tabName}`;
        history.pushState({tab: tabName}, '', url);

        // Show loading indicator
        const contentDiv = document.getElementById('tab-content');
        contentDiv.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
            </div>
        `;

        // Load content via AJAX
        fetch(`/staff/ajax/${tabName}`)
            .then(response => {
                if (response.status === 423) { window.location.reload(); throw new Error('Session locked'); }
                if (!response.ok) throw new Error(`Unable to load this section (${response.status})`);
                return response.text();
            })
            .then(html => {
                contentDiv.innerHTML = html;
                updateNavHighlight();
                initTabScripts(tabName);
            })
            .catch(error => {
                contentDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-bold">Error loading content</p>
                        <p>${error.message}</p>
                    </div>
                `;
            });
    }

    function initTabScripts(tabName) {
        const contentDiv = document.getElementById('tab-content');

        // Find all script tags in the loaded content
        const scripts = contentDiv.querySelectorAll('script');

        scripts.forEach(oldScript => {
            // Clone the script to execute it
            const newScript = document.createElement('script');

            // Copy all attributes
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });

            // Copy the script content
            newScript.textContent = oldScript.textContent;

            // Replace old script with new one
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }

    // Handle browser back/forward
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.tab) {
            switchTab(e.state.tab);
        }
    });

    // Initialize with dashboard tab and update nav
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname;
        const tabMatch = path.match(/\/staff(?:\/([\w-]+))?/);
        const initialTab = tabMatch && tabMatch[1] ? tabMatch[1] : 'dashboard';
        updateNavHighlight();
        switchTab(initialTab);
    });
</script>
@endsection
