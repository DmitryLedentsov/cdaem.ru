$.reject({
    // Specifies which browsers/versions will be blocked
    reject: {
        all: false, // Covers Everything (Nothing blocked)
        msie: 10 // Covers MSIE <= 6 (Blocked by default)
        /*
         * Many possible combinations.
         * You can specify browser (msie, chrome, firefox)
         * You can specify rendering engine (geko, trident)
         * You can specify OS (Win, Mac, Linux, Solaris, iPhone, iPad)
         *
         * You can specify versions of each.
         * Examples: msie9: true, firefox8: true,
         *
         * You can specify the highest number to reject.
         * Example: msie: 9 (9 and lower are rejected.
         *
         * There is also "unknown" that covers what isn't detected
         * Example: unknown: true
         */
    },
    display: [], // What browsers to display and their order (default set below)
    browserShow: true, // Should the browser options be shown?
    browserInfo: { // Settings for which browsers to display
        chrome: {
            // Text below the icon
            text: 'Google Chrome',
            // URL For icon/text link
            url: 'http://www.google.com/chrome/'
            // (Optional) Use "allow" to customized when to show this option
            // Example: to show chrome only for IE users
            // allow: { all: false, msie: true }
        },
        firefox: {
            text: 'Mozilla Firefox',
            url: 'http://www.mozilla.com/firefox/'
        },
        safari: {
            text: 'Safari',
            url: 'http://www.apple.com/safari/download/'
        },
        opera: {
            text: 'Opera',
            url: 'http://www.opera.com/download/'
        }
        /*msie: {
         text: 'Internet Explorer',
         url: 'http://www.microsoft.com/windows/Internet-explorer/'
         }*/
    },

    // Pop-up Window Text
    header: 'Did you know that your Internet Browser is out of date?',

    paragraph1: 'Your browser is out of date, and may not be compatible with our website. A list of the most popular web browsers can be found below.',

    paragraph2: 'Just click on the icons to get to the download page',

    // Allow closing of window
    close: false,

    // Message displayed below closing link
    closeMessage: 'By closing this window you acknowledge that your experience on this website may be degraded',
    closeLink: 'Close This Window',
    closeURL: '#',

    // Allows closing of window with esc key
    closeESC: false,

    // Use cookies to remmember if window was closed previously?
    closeCookie: false,
    // Cookie settings are only used if closeCookie is true
    cookieSettings: {
        // Path for the cookie to be saved on
        // Should be root domain in most cases
        path: '/',
        // Expiration Date (in seconds)
        // 0 (default) means it ends with the current session
        expires: 0
    },

    // Path where images are located
    imagePath: '/statics/public/widgets/browsers/',
    // Background color for overlay
    overlayBgColor: '#000',
    // Background transparency (0-1)
    overlayOpacity: 0.8,

    // Fade in time on open ('slow','medium','fast' or integer in ms)
    fadeInTime: 'fast',
    // Fade out time on close ('slow','medium','fast' or integer in ms)
    fadeOutTime: 'fast',

    // Google Analytics Link Tracking (Optional)
    // Set to true to enable
    // Note: Analytics tracking code must be added separately
    analytics: false
});