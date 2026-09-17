(function () {
    // IIFE - prevents leaking variables into host site's global scope
    var payload = {
        page_url: window.location.href,
        referrer: document.referrer || null
    };

    // visitor_id and user_agent are intentionally NOT sent from here -
    // the server reads them itself (cookie / HTTP header)

    fetch('/track', { // absolute path - this snippet is embadded on third-party sites
        method: 'POST',
        headers: {
            'Content-Type': 'application-json'
        },
        credentials: 'include', // required to send/receive the visitor_id cookie
        body: JSON.stringify(payload)
    });
})();