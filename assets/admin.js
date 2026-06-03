(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var button = document.getElementById('snworks-cron-diagnostics-copy-report');
        var report = document.getElementById('snworks-cron-diagnostics-report');
        var status = document.getElementById('snworks-cron-diagnostics-copy-status');

        if (!button || !report || !status) {
            return;
        }

        var markCopied = function () {
            status.textContent = status.getAttribute('data-copied-text') || 'Copied.';
        };

        button.addEventListener('click', function () {
            report.focus();
            report.select();

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(report.value).then(markCopied).catch(function () {
                    document.execCommand('copy');
                    markCopied();
                });
                return;
            }

            document.execCommand('copy');
            markCopied();
        });
    });
}());
