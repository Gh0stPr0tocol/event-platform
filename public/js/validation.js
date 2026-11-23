/* Shared client-side validation. PHP server validation remains authoritative. */

const Validators = {
    required(value) {
        return value.trim() === '' ? 'This field is required.' : '';
    },
    email(value) {
        if (!value.trim()) return '';
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim())
            ? ''
            : 'Enter a valid email address.';
    },
    phone(value) {
        if (!value.trim()) return '';
        const phone = value.replace(/[\s()-]/g, '');
        return /^(?:07\d{8}|01\d{8}|\+254(?:7|1)\d{8})$/.test(phone)
            ? ''
            : 'Enter a valid Kenyan phone number, like 0712345678';
    },
    minLength(value, length) {
        return value.length >= Number(length)
            ? ''
            : `Use at least ${length} characters.`;
    },
    maxLength(value, length) {
        return value.length <= Number(length)
            ? ''
            : `Use no more than ${length} characters.`;
    },
    password(value) {
        if (!value) return '';
        if (value.length < 8) return 'Password must be at least 8 characters.';
        if (!/[A-Za-z]/.test(value) || !/\d/.test(value)) {
            return 'Password needs at least one letter and one number.';
        }
        return '';
    },
    match(value, selector, form) {
        const matchField = form.querySelector(selector);
        return matchField && value !== matchField.value
            ? 'Passwords do not match'
            : '';
    },
    futureDate(value) {
        if (!value) return '';
        const selected = new Date(`${value}T00:00:00`);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return selected >= today ? '' : 'Event date cannot be in the past.';
    },
    intRange(value, min, max) {
        if (!value) return '';
        const number = Number(value);
        return Number.isInteger(number) && number >= Number(min) && number <= Number(max)
            ? ''
            : `Capacity must be between ${min} and ${max}.`;
    },
    image(fileInput) {
        if (!fileInput.files || fileInput.files.length === 0) return '';
        const file = fileInput.files[0];
        const typeAllowed = ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
        const nameAllowed = /\.(jpe?g|png|webp)$/i.test(file.name);
        if (!typeAllowed || !nameAllowed) return 'Choose a JPG, PNG or WebP image.';
        if (file.size > 2 * 1024 * 1024) return 'Image must be 2 MB or smaller.';
        return '';
    },
};

const formConfigs = [
    {
        selector: 'form[action*="action=register"]',
        fields: {
            name: ['required', 'minLength:2', 'maxLength:100'],
            email: ['required', 'email', 'maxLength:150'],
            phone: ['required', 'phone', 'maxLength:20'],
            region_id: ['required'],
            password: ['required', 'password', 'minLength:8'],
            password_confirmation: ['required', 'match:[name="password"]'],
        },
    },
    {
        selector: 'form[action*="action=login"]',
        fields: {
            email: ['required', 'email'],
            password: ['required'],
        },
    },
    {
        selector: 'form[action*="action=updateProfile"]',
        fields: {
            name: ['required', 'minLength:2', 'maxLength:100'],
            phone: ['required', 'phone', 'maxLength:20'],
            region_id: ['required'],
        },
    },
    {
        selector: 'form[action*="action=changePassword"]',
        fields: {
            current_password: ['required'],
            new_password: ['required', 'password', 'minLength:8'],
            new_password_confirmation: ['required', 'match:[name="new_password"]'],
        },
    },
    {
        selector: 'form[action*="action=eventStore"], form[action*="action=eventUpdate"]',
        fields: {
            title: ['required', 'maxLength:150'],
            description: ['required', 'maxLength:5000'],
            category_id: ['required'],
            venue_id: ['required'],
            event_date: ['required', 'futureDate'],
            start_time: ['required'],
            capacity: ['required', 'intRange:1:10000'],
            image: ['image'],
        },
    },
    {
        selector: 'form[action*="action=categoryStore"], form[action*="action=categoryUpdate"]',
        fields: {
            name: ['required', 'maxLength:100'],
        },
    },
    {
        selector: 'form[action*="action=venueStore"], form[action*="action=venueUpdate"]',
        fields: {
            name: ['required', 'maxLength:150'],
            address: ['required', 'maxLength:255'],
            region_id: ['required'],
        },
    },
    {
        selector: 'form.event-search',
        fields: {
            date_from: [],
            date_to: [],
        },
        crossField(dateFromField, dateToField) {
            if (!dateFromField.value || !dateToField.value) return null;
            if (dateFromField.value > dateToField.value) {
                return {
                    field: dateToField,
                    message: 'The end date must be on or after the start date.',
                };
            }
            return null;
        },
    },
];

function parseRule(rule) {
    const parts = rule.split(':');
    return { name: parts.shift(), args: parts };
}

function validateField(field, rules = [], form) {
    let message = '';
    const isFile = field.type === 'file';

    for (const ruleText of rules) {
        const rule = parseRule(ruleText);
        const validator = Validators[rule.name];
        if (!validator) continue;

        if (rule.name === 'match') {
            message = validator(field.value, rule.args[0], form);
        } else if (rule.name === 'intRange') {
            message = validator(field.value, rule.args[0], rule.args[1]);
        } else if (rule.name === 'minLength') {
            message = validator(field.value, rule.args[0]);
        } else if (rule.name === 'maxLength') {
            message = validator(field.value, rule.args[0]);
        } else if (isFile) {
            message = validator(field);
        } else {
            message = validator(field.value);
        }

        if (message) break;
    }

    return message;
}

function getOrCreateError(field) {
    let error = field.parentElement.querySelector('[data-js-error]');
    if (!error) {
        error = field.parentElement.querySelector('.field-error');
        if (error) {
            error.setAttribute('data-js-error', '');
            error.classList.add('js-field-error');
        }
    }
    if (!error) {
        error = document.createElement('p');
        error.className = 'field-error js-field-error';
        error.setAttribute('data-js-error', '');
        error.setAttribute('role', 'alert');
        field.parentElement.appendChild(error);
    }
    return error;
}

function showError(field, message) {
    const error = getOrCreateError(field);
    error.textContent = message;
    error.hidden = false;
    field.classList.remove('valid');
    field.classList.add('invalid');
    field.setAttribute('aria-invalid', 'true');
    const errorId = error.id || `error-${field.id || field.name}`;
    error.id = errorId;
    field.setAttribute('aria-describedby',
        [field.getAttribute('aria-describedby'), errorId].filter(Boolean).join(' ').trim()
    );
}

function showSuccess(field) {
    const error = field.parentElement.querySelector('[data-js-error]');
    if (error) {
        error.textContent = 'Looks good';
        error.className = 'field-success';
        error.hidden = false;
    }
    field.classList.remove('invalid');
    field.classList.add('valid');
    field.setAttribute('aria-invalid', 'false');
}

function clearState(field) {
    const error = field.parentElement.querySelector('[data-js-error]');
    if (error) {
        error.textContent = '';
        error.hidden = true;
        error.className = 'field-error js-field-error';
    }
    field.classList.remove('invalid', 'valid');
    field.removeAttribute('aria-invalid');
}

function validateForm(form, config) {
    let firstInvalid = null;

    Object.entries(config.fields).forEach(([name, rules]) => {
        const field = form.elements.namedItem(name);
        if (!field) return;
        const message = validateField(field, rules, form);
        if (message) {
            showError(field, message);
            if (!firstInvalid) firstInvalid = field;
        } else {
            showSuccess(field);
        }
    });

    if (typeof config.crossField === 'function') {
        const dateFrom = form.elements.namedItem('date_from');
        const dateTo = form.elements.namedItem('date_to');
        if (dateFrom && dateTo) {
            const crossError = config.crossField(dateFrom, dateTo);
            if (crossError) {
                showError(crossError.field, crossError.message);
                if (!firstInvalid) firstInvalid = crossError.field;
            } else {
                clearState(dateFrom);
                clearState(dateTo);
            }
        }
    }

    return firstInvalid;
}

function addPasswordToggle(field) {
    if (field.dataset.toggleAdded) return;
    field.dataset.toggleAdded = 'true';
    field.classList.add('password-input');

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'password-toggle';
    button.textContent = 'Show';
    button.setAttribute('aria-label', 'Show password');
    button.addEventListener('click', () => {
        const show = field.type === 'password';
        field.type = show ? 'text' : 'password';
        button.textContent = show ? 'Hide' : 'Show';
        button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
    field.parentElement.appendChild(button);
}

function updatePasswordStrength(field) {
    let hint = field.parentElement.querySelector('.password-strength');
    if (!hint) {
        hint = document.createElement('p');
        hint.className = 'password-strength';
        hint.setAttribute('aria-live', 'polite');
        field.parentElement.appendChild(hint);
    }
    const value = field.value;
    if (!value) {
        hint.textContent = '';
        hint.className = 'password-strength';
        return;
    }

    let score = 0;
    if (value.length >= 8) score++;
    if (/[A-Za-z]/.test(value) && /\d/.test(value)) score++;
    if (/[A-Z]/.test(value) && /[a-z]/.test(value) && /[^A-Za-z0-9]/.test(value)) score++;

    const labels = ['Weak', 'Okay', 'Strong'];
    const classes = ['weak', 'okay', 'strong'];
    hint.textContent = `Password strength: ${labels[Math.min(score, 2)]}`;
    hint.className = `password-strength ${classes[Math.min(score, 2)]}`;
}

function initForm(form, config) {
    form.noValidate = true;
    const touched = new WeakSet();

    Object.entries(config.fields).forEach(([name, rules]) => {
        const field = form.elements.namedItem(name);
        if (!field) return;

        if (field.type === 'password') addPasswordToggle(field);

        const serverError = field.parentElement.querySelector('.field-error');
        if (serverError) {
            serverError.setAttribute('data-js-error', '');
            serverError.classList.add('js-field-error');
            serverError.setAttribute('role', 'alert');
            showError(field, serverError.textContent);
        } else {
            clearState(field);
        }
        field.addEventListener('blur', () => {
            touched.add(field);
            const message = validateField(field, rules, form);
            if (message) showError(field, message);
            else showSuccess(field);
        });
        field.addEventListener('input', () => {
            if (touched.has(field)) {
                const message = validateField(field, rules, form);
                if (message) showError(field, message);
                else showSuccess(field);
            }
            if (name === 'new_password') updatePasswordStrength(field);
            const confirmationName = name === 'password'
                ? 'password_confirmation'
                : (name === 'new_password' ? 'new_password_confirmation' : null);
            if (confirmationName) {
                const confirmation = form.elements.namedItem(confirmationName);
                if (confirmation && touched.has(confirmation)) {
                    const confirmationRules = config.fields[confirmationName] || [];
                    const confirmationMessage = validateField(confirmation, confirmationRules, form);
                    if (confirmationMessage) showError(confirmation, confirmationMessage);
                    else showSuccess(confirmation);
                }
            }
        });
        field.addEventListener('change', () => {
            if (touched.has(field)) {
                const message = validateField(field, rules, form);
                if (message) showError(field, message);
                else showSuccess(field);
            }
        });
    });

    const strengthField = form.elements.namedItem('new_password');
    if (strengthField) updatePasswordStrength(strengthField);

    if (typeof config.crossField === 'function') {
        const dateFrom = form.elements.namedItem('date_from');
        const dateTo = form.elements.namedItem('date_to');
        if (dateFrom && dateTo) {
            const validateRange = () => {
                const crossError = config.crossField(dateFrom, dateTo);
                if (crossError) {
                    showError(crossError.field, crossError.message);
                } else {
                    clearState(dateFrom);
                    clearState(dateTo);
                }
            };
            dateFrom.addEventListener('change', validateRange);
            dateTo.addEventListener('change', validateRange);
        }
    }

    form.addEventListener('submit', (event) => {
        const firstInvalid = validateForm(form, config);
        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
            return;
        }

        event.preventDefault();
        let success = form.querySelector('.form-submit-success');
        if (!success) {
            success = document.createElement('p');
            success.className = 'form-submit-success';
            success.setAttribute('role', 'status');
            const submit = form.querySelector('[type="submit"]');
            submit.parentElement.insertBefore(success, submit);
        }
        success.textContent = 'All good, submitting...';
        window.setTimeout(() => form.submit(), 250);
    });
}

function initFlashMessages() {
    document.querySelectorAll('.flash').forEach((flash) => {
        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'flash-close';
        close.textContent = '×';
        close.setAttribute('aria-label', 'Dismiss message');
        close.addEventListener('click', () => flash.remove());
        flash.appendChild(close);

        if (flash.classList.contains('flash-success')) {
            window.setTimeout(() => flash.remove(), 6000);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    formConfigs.forEach((config) => {
        document.querySelectorAll(config.selector).forEach((form) => initForm(form, config));
    });
    initFlashMessages();
});