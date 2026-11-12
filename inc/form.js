// Sends the membership form to /proc/ in the background (URI-encoded, like a
// normal form post) and shows the result on the same page. If anything goes
// wrong here, the form is posted the normal way instead.
const form   = document.getElementById('membershipForm');
const result = document.getElementById('formResult');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = form.querySelector('.submit-btn');
    button.disabled = true;
    button.textContent = 'Submitting...';

    let response;
    try {
        response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'fetch'
            },
            body: new URLSearchParams(new FormData(form)).toString()
        });
    } catch (err) {
        form.submit();
        return;
    }

    const html = await response.text();
    if (response.ok) {
        // Saved: the thanks replaces the form.
        result.innerHTML = '';
        form.closest('.form-container').outerHTML = html;
    } else {
        // Needs fixing: errors above the form, answers left as typed.
        result.innerHTML = html;
        button.disabled = false;
        button.textContent = 'Submit Application';
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
