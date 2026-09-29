/*
 * kj_comment: posts and deletes comments without reloading the download page.
 * The server answers with the comment box rendered again (comment.html of the style, or the box of the plugin)
 * and a new form key, the list of the page is replaced by the list of that box.
 * Without JavaScript the forms are posted as usual and the page is reloaded.
 *
 * It works with every comment box, a comment.html of a style too, the box only needs these:
 * - #kj-comments                  the box, with
 *     data-confirm-delete         question before deleting a comment
 *     data-error                  message when the server can not be reached
 *     data-alert-success          classes of a success message, like "alert alert-success"
 *     data-alert-danger           classes of an error message
 * - [data-kj-form]                form of a new comment, with its textarea and submit button (guests have none)
 * - [data-kj-counter]             optional, "0 / 1000" of the textarea
 * - [data-kj-count]               number of the comments
 * - [data-kj-status]              where the messages are shown
 * - [data-kj-list]                the comments, replaced by the [data-kj-list] of the box of every answer
 * - [data-kj-comment="id"]        a comment of the list
 * - form[data-kj-delete]          delete form of a comment
 * and the style gives these classes a look:
 * - .is-busy on a button waiting for the server, .is-limit on the counter near the limit,
 *   .is-new on the comment just posted, .is-removing on the comment being deleted
 */
(function () {
    'use strict';

    var box = document.getElementById('kj-comments');

    if (!box || !window.fetch || !window.FormData) {
        return;
    }

    var list = box.querySelector('[data-kj-list]');
    var count = box.querySelector('[data-kj-count]');
    var status = box.querySelector('[data-kj-status]');
    var form = box.querySelector('[data-kj-form]');
    var hideTimer;

    // a short message under the form, the success ones go away by themselves
    function say(message, type) {
        var alert = document.createElement('div');

        clearTimeout(hideTimer);
        alert.className = box.getAttribute('data-alert-' + type) || 'kj-alert kj-alert-' + type;
        alert.textContent = message;
        status.textContent = '';
        status.appendChild(alert);

        if (type === 'success') {
            hideTimer = setTimeout(function () {
                status.textContent = '';
            }, 4000);
        }
    }

    function busy(button, on) {
        button.disabled = on;
        button.classList.toggle('is-busy', on);
        button.setAttribute('aria-busy', on ? 'true' : 'false');
    }

    // every answer has a new form key, the old one expires while the page stays open
    function refreshKeys(html) {
        if (!html) {
            return;
        }

        var holder = document.createElement('div');

        holder.innerHTML = html;
        holder.querySelectorAll('input[name]').forEach(function (fresh) {
            box.querySelectorAll('input[name="' + fresh.name + '"]').forEach(function (input) {
                input.value = fresh.value;
            });
        });
    }

    // the answer has the whole box, only its list is taken, a template parses it without loading anything
    function render(data) {
        if (typeof data.box === 'string') {
            var fresh = document.createElement('template');

            fresh.innerHTML = data.box;

            var freshList = fresh.content.querySelector('[data-kj-list]');

            if (freshList) {
                list.innerHTML = freshList.innerHTML;
            }
        }

        if (typeof data.count === 'number') {
            count.textContent = data.count;
        }

        refreshKeys(data.form_key);
    }

    function wait(ms) {
        return new Promise(function (resolve) {
            setTimeout(resolve, ms);
        });
    }

    function send(sender, retried) {
        return fetch(sender.action, {
            method: 'POST',
            body: new FormData(sender),
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
        }).then(function (response) {
            return response
                .json()
                .catch(function () {
                    return {};
                })
                .then(function (data) {
                    if (response.ok && data.ok) {
                        return data;
                    }

                    refreshKeys(data.form_key);

                    // the key expired while the page was open, or was used in the second it was made
                    // (kleeja refuses that), so it is sent once more with the new key, a second later
                    if (data.expired_key && data.form_key && !retried) {
                        return wait(1100).then(function () {
                            return send(sender, true);
                        });
                    }

                    throw new Error(data.message || box.getAttribute('data-error'));
                });
        });
    }

    // a network error has its own message from the browser, ours is clearer
    function fail(error) {
        say(error instanceof TypeError ? box.getAttribute('data-error') : error.message, 'danger');
    }


    /*
     * a new comment
     */
    if (form) {
        var textarea = form.querySelector('textarea');
        var counter = form.querySelector('[data-kj-counter]');
        var submit = form.querySelector('[type="submit"]');
        var max = parseInt(textarea.getAttribute('maxlength'), 10) || 0;

        var updateCounter = function () {
            var length = textarea.value.length;

            if (counter) {
                counter.textContent = length + ' / ' + max;
                counter.classList.toggle('is-limit', max > 0 && length >= max * 0.9);
            }

            submit.disabled = textarea.value.trim() === '' || submit.classList.contains('is-busy');
        };

        textarea.addEventListener('input', updateCounter);

        // ctrl + enter (cmd + enter on mac) posts the comment
        textarea.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && (event.ctrlKey || event.metaKey) && !submit.disabled) {
                event.preventDefault();
                submit.click();
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (textarea.value.trim() === '') {
                textarea.focus();

                return;
            }

            busy(submit, true);
            textarea.readOnly = true;

            send(form)
                .then(function (data) {
                    render(data);
                    textarea.value = '';
                    say(data.message, 'success');

                    var added = list.querySelector('[data-kj-comment="' + data.id + '"]');

                    if (added) {
                        added.classList.add('is-new');
                    }
                })
                .catch(fail)
                .then(function () {
                    busy(submit, false);
                    textarea.readOnly = false;
                    updateCounter();
                });
        });

        updateCounter();
    }


    /*
     * delete a comment, the list is replaced after posting, so the forms are found from the list
     */
    list.addEventListener('submit', function (event) {
        var sender = event.target.closest('[data-kj-delete]');

        if (!sender) {
            return;
        }

        event.preventDefault();

        if (!window.confirm(box.getAttribute('data-confirm-delete'))) {
            return;
        }

        var item = sender.closest('[data-kj-comment]');
        var button = sender.querySelector('[type="submit"]');

        busy(button, true);

        send(sender)
            .then(function (data) {
                item.classList.add('is-removing');

                setTimeout(function () {
                    render(data);
                    say(data.message, 'success');
                }, 250);
            })
            .catch(function (error) {
                busy(button, false);
                fail(error);
            });
    });
})();
