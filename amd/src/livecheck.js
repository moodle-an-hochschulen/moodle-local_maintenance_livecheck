/**
 * Local plugin "Maintenance mode (live check)" - JS Code
 *
 * @module   local_maintenance_livecheck/livecheck
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/log', 'core/config'], function($, str, log, config) {
    "use strict";

    // Global variable for left time.
    var timeleftinsec = null;

    // Global variable for countdown interval.
    var countdownInterval = null;

    // Global variable for maintenance announcement box content.
    var boxContent = null;

    /**
     * Function which shows the maintenance announcement box.
     */
    function showBox() {
        // If maintenance announcement box does not have content already.
        if (boxContent === null) {
            // Create the maintenance announcement box content and save it globally.
            $('#maintenance_announcement').append('<div class="box py-3 moodle-has-zindex maintenancewarning alert"' +
                    'aria-live="polite">');
            boxContent = $('.box.maintenancewarning');
        }
    }

    /**
     * Function which hides the maintenance announcement box.
     */
    function hideBox() {
        // If maintenance announcement box still has content.
        if (boxContent !== null) {
            // Remove the content and reset the box content's global variable.
            $('#maintenance_announcement').empty();
            boxContent = null;
        }
    }

    /**
     * This function fetches the necessary strings once and caches them locally in the browser.
     * This is needed because we won't be able to get the sitemaintenance string anymore from the server as soon as
     * maintenance mode has started.
     */
    function cacheStrings() {
        str.get_strings([
                {key: 'sitemaintenance', component: 'admin'},
                {key: 'maintenancemodeisscheduled', component: 'admin'},
                {key: 'maintenancemodeisscheduledlong', component: 'admin'}
            ]);
    }

    /**
     * Function to update the countdown in the maintenance announcement box.
     */
    function updateCountdown() {
        // Reduce left time globally.
        timeleftinsec -= 1;

        // Maintenance mode has started.
        if (timeleftinsec <= 0) {
            str.get_string('sitemaintenance', 'admin').done(function(s) {
                boxContent.html(s);
            });

            // Maintenance mode is still scheduled.
        } else {
            var a = {};
            a.sec = Math.floor(timeleftinsec % 60);
            a.min = Math.floor(timeleftinsec / 60) % 60;
            a.hour = Math.floor(timeleftinsec / 3600);
            if (a.hour > 0) {
                str.get_string('maintenancemodeisscheduledlong', 'admin', a).done(function(s) {
                    boxContent.html(s);
                });
            } else {
                str.get_string('maintenancemodeisscheduled', 'admin', a).done(function(s) {
                    boxContent.html(s);
                });
            }
        }

        // Set maintenance announcement box class to highlight the importance.
        if (timeleftinsec < 30) {
            boxContent.addClass('alert-error').addClass('alert-danger').removeClass('alert-warning');
        } else {
            boxContent.addClass('alert-warning').removeClass('alert-error').removeClass('alert-error');
        }
    }

    /**
     * Function which performs the continuous live check.
     */
    function checkStatus() {
        // Fetch maintenance mode status by AJAX.
        // We know about the benefits of the core/ajax module (https://docs.moodle.org/dev/AJAX),
        // but for this very lightweight check we only use a simple jQuery AJAX call.
        $.ajax({
            url: config.wwwroot + '/local/maintenance_livecheck/ajax.php',
            dataType: 'json',
            type: 'POST',
            data: {
                // Add a query string to prevent older versions of IE from using the cache.
                'time': Date.now()
            },
            headers: {
                'Cache-Control': 'no-cache',
                'Expires': '-1'
            },
            success: function(result) {
                // If CLI maintenance mode is not scheduled or active.
                if (result.timeleftinsec === null) {
                    // Hide the maintenance announcement box.
                    hideBox();

                    // Clear the left time globally.
                    timeleftinsec = null;

                    // Stop the countdown interval.
                    clearInterval(countdownInterval);

                    // Otherwise, if CLI maintenance mode is scheduled.
                } else if (result.timeleftinsec !== null && result.timeleftinsec > 0) {
                    // Show the maintenance announcement box.
                    showBox();

                    // Store the left time globally.
                    timeleftinsec = result.timeleftinsec;

                    // Re-Start the countdown interval.
                    clearInterval(countdownInterval);
                    countdownInterval = setInterval(updateCountdown, 1000);

                    // Otherwise, if legacy maintenance mode is active.
                } else if (result.timeleftinsec !== null && result.timeleftinsec === 0) {
                    // Show the maintenance announcement box.
                    showBox();

                    // Store the left time globally.
                    timeleftinsec = 0;

                    // Stop the countdown and run it once to show the sitemaintenance message.
                    clearInterval(countdownInterval);
                    updateCountdown();
                }
            },
            error: function(request) {
                // If CLI maintenance mode is active.
                if (request.status == 503 && request.statusText == 'Moodle under maintenance') {
                    // Show the maintenance announcement box.
                    showBox();

                    // Store the left time globally.
                    timeleftinsec = 0;

                    // Stop the countdown and run it once to show the sitemaintenance message.
                    clearInterval(countdownInterval);
                    updateCountdown();

                    // The AJAX call was cached somewhere.
                } else if (request.status >= 300 && request.status <= 399) {
                    // Warn the developer.
                    log.debug('moodle-local_maintenance_livecheck-livecheck: ' +
                            'A cached copy of the live check answer was returned so it\'s reliablity cannot be guaranteed. ' +
                            'Hiding the maintenance announcement box now.');

                    // Hide the maintenance announcement box.
                    hideBox();

                    // Clear the left time globally.
                    timeleftinsec = null;

                    // Stop the countdown interval.
                    clearInterval(countdownInterval);
                }
            }
        });
    }

    return {
        init: function(params) {
            // Initialize continuous live check.
            if (params.checkinterval !== null && params.checkinterval > 0) {
                // Add maintenance announcement box to page.
                $('body').append('<div id="maintenance_announcement">');

                // If a back off time is set.
                if (params.backoff !== null && params.backoff > 0) {
                    // Wait params.backoff seconds and then check status every params.checkinterval seconds.
                    setTimeout(function() {
                        setInterval(checkStatus, params.checkinterval * 1000);
                    }, params.backoff * 1000);

                    // Otherwise if no back off time is set.
                } else {
                    // Check status every params.checkinterval seconds.
                    setInterval(checkStatus, params.checkinterval * 1000);
                }

                // Cache strings in browser.
                cacheStrings();
            }
        }
    };
});
