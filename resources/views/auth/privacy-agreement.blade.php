<x-guest-layout :wide="true">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Data Privacy Agreement and Terms of Use
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Version {{ $termsVersion }} &middot; Effective {{ $effectiveDate }}
        </p>
    </div>

    <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-200">
        You have successfully verified your login credentials. Please review this agreement before accessing your account in {{ $organization }}. This notice is intended to support transparency, legitimate purpose, and proportionality under the Philippine Data Privacy Act of 2012 and applicable institutional policies.
    </div>

    <div class="max-h-[55vh] space-y-5 overflow-y-auto rounded-lg border border-gray-200 bg-white p-5 text-sm leading-6 text-gray-700 shadow-inner dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">1. Information We Process</h2>
            <p class="mt-1">
                The system may collect and process account details, identity and contact information, school-related information, appointment data, medical or dental records, prescriptions, immunization information, uploaded documents, and system activity required to provide clinic services.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">2. Purpose of Processing</h2>
            <p class="mt-1">
                Information is used for patient identification, appointment scheduling, consultation and treatment documentation, issuance of clinic records or certificates, medicine and inventory management, reporting, system administration, security, and other legitimate school-clinic functions.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">3. Access and Disclosure</h2>
            <p class="mt-1">
                Access is restricted according to assigned roles and institutional procedures. Information may be disclosed to authorized school or clinic personnel, health professionals, service providers, regulators, or other parties when permitted or required by law and when necessary for the stated purposes.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">4. Security and Confidentiality</h2>
            <p class="mt-1">
                Reasonable administrative, physical, and technical safeguards are used to protect information. You must keep your password confidential, use only your own account, avoid unauthorized access or disclosure, and immediately report suspected account compromise.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">5. Retention</h2>
            <p class="mt-1">
                Records may be retained for as long as necessary for healthcare, academic, administrative, legal, security, and records-management purposes, after which they may be securely archived, anonymized, or disposed of under applicable policies.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">6. Your Data Privacy Rights</h2>
            <p class="mt-1">
                Subject to applicable law and institutional procedures, you may request information about the processing of your personal data, access or correction of records, and assistance regarding privacy concerns. Certain requests may be limited where retention or processing is required by law or necessary for legitimate institutional functions.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">7. Acceptable Use</h2>
            <p class="mt-1">
                You agree not to misuse the system, access records without authorization, share another person's confidential information, alter or destroy records improperly, bypass security controls, or use the system for unlawful purposes. System activity may be logged for security and accountability.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-900 dark:text-white">8. Acknowledgment</h2>
            <p class="mt-1">
                By selecting “I Agree and Continue,” you confirm that you have read and understood this notice, agree to the system's Terms of Use, and acknowledge the processing of your information for the purposes described above. Declining will end this login session and return you to the login page.
            </p>
        </section>

        <section class="rounded-md bg-gray-50 p-3 dark:bg-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Privacy Questions</h2>
            <p class="mt-1">
                Contact the school or clinic administration regarding privacy requests or concerns.
                @if ($contactEmail)
                    You may also email
                    <a class="font-medium text-indigo-700 underline dark:text-indigo-300" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.
                @endif
            </p>
        </section>
    </div>

    @if ($errors->has('agreement'))
        <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $errors->first('agreement') }}</p>
    @endif

    <div class="mt-6 grid gap-3 sm:grid-cols-2">
        <form method="POST" action="{{ route('privacy-agreement.decline') }}">
            @csrf
            <button type="submit" class="w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                I Do Not Agree
            </button>
        </form>

        <form method="POST" action="{{ route('privacy-agreement.accept') }}">
            @csrf
            <input type="hidden" name="agreement" value="1">
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                I Agree and Continue
            </button>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
        Acceptance date, agreement version, IP address, and browser information are recorded for accountability.
    </p>
</x-guest-layout>
