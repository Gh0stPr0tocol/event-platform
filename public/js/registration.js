// Public event interactions: saved events, seat availability, and registration confirmation.
const SavedEvents = {
    legacyStorageKey: 'tukutane.savedEvents.v1',
    storageKey: 'tukutane.savedEvents.v1',
    ids: [],
    storageAvailable: true,
    authenticated: false,
    apiUrl: '',
    importUrl: '',
    csrfToken: '',

    read(key = this.storageKey) {
        try {
            const stored = window.localStorage.getItem(key);
            if (!stored) return [];

            const parsed = JSON.parse(stored);
            if (!Array.isArray(parsed)) return [];
            return [...new Set(parsed.map(String).filter((id) => /^\d+$/.test(id) && Number(id) > 0))];
        } catch (error) {
            this.storageAvailable = false;
            return [];
        }
    },

    persist() {
        try {
            window.localStorage.setItem(this.storageKey, JSON.stringify(this.ids));
            this.storageAvailable = true;
            return true;
        } catch (error) {
            this.storageAvailable = false;
            return false;
        }
    },

    normalizeIds(value) {
        if (!Array.isArray(value)) return [];
        return [...new Set(value.map(String).filter((id) => /^\d+$/.test(id) && Number(id) > 0))];
    },

    renderButtons() {
        document.querySelectorAll('[data-save-event]').forEach((button) => {
            const id = String(button.dataset.eventId);
            const saved = this.ids.includes(id);
            const title = button.dataset.eventTitle || 'event';
            const heart = button.querySelector('.save-event-heart');

            button.setAttribute('aria-pressed', String(saved));
            button.setAttribute('aria-label', `${saved ? 'Remove' : 'Save'} ${title} ${saved ? 'from' : 'to'} saved events`);
            button.classList.toggle('is-saved', saved);
            if (heart) heart.textContent = saved ? '♥' : '♡';
        });

        document.querySelectorAll('[data-saved-count]').forEach((badge) => {
            badge.textContent = String(this.ids.length);
            badge.hidden = this.ids.length === 0;
            badge.setAttribute('aria-label', `${this.ids.length} saved ${this.ids.length === 1 ? 'event' : 'events'}`);
        });
    },

    applyFilter() {
        const toggle = document.querySelector('[data-saved-filter]');
        const grid = document.querySelector('[data-saved-event-grid]');
        const emptyMessage = document.querySelector('[data-saved-filter-empty]');
        if (!toggle || !grid) return;

        let visibleCount = 0;
        grid.querySelectorAll('.event-card').forEach((card) => {
            const button = card.querySelector('[data-save-event]');
            const isSaved = button && this.ids.includes(String(button.dataset.eventId));
            card.hidden = toggle.checked && !isSaved;
            if (!card.hidden) visibleCount++;
        });

        if (emptyMessage) emptyMessage.hidden = !toggle.checked || visibleCount > 0;
    },

    updateSavedQuery(isSavedOnly) {
        const form = document.querySelector('form.event-search');
        const hiddenInput = form?.querySelector('[data-saved-param]');
        if (hiddenInput) {
            hiddenInput.value = '1';
            hiddenInput.disabled = !isSavedOnly;
        }

        const url = new URL(window.location.href);
        if (isSavedOnly) url.searchParams.set('saved', '1');
        else url.searchParams.delete('saved');
        window.history.replaceState(null, '', url);

        const countLabel = document.querySelector('[data-active-filter-count]');
        if (countLabel) {
            const count = Number(countLabel.dataset.baseCount || 0) + (isSavedOnly ? 1 : 0);
            countLabel.textContent = `${count} active ${count === 1 ? 'filter' : 'filters'}`;
            countLabel.hidden = count === 0;
        }
        const clearLink = document.querySelector('[data-clear-filters]');
        if (clearLink) clearLink.hidden = Number(countLabel?.dataset.baseCount || 0) + (isSavedOnly ? 1 : 0) === 0;
    },

    setButtonsDisabled(disabled, eventId = null) {
        document.querySelectorAll('[data-save-event]').forEach((button) => {
            if (eventId !== null && String(button.dataset.eventId) !== eventId) return;
            button.disabled = disabled;
            if (disabled) button.setAttribute('aria-busy', 'true');
            else button.removeAttribute('aria-busy');
        });
    },

    async request(url, values = null) {
        const options = { method: values ? 'POST' : 'GET', credentials: 'same-origin' };
        if (values) {
            values.set('csrf_token', this.csrfToken);
            options.headers = { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' };
            options.body = values.toString();
        }

        const response = await fetch(url, options);
        let payload;
        try {
            payload = await response.json();
        } catch (error) {
            throw new Error('The saved-events service returned an unreadable response.');
        }
        if (!response.ok) {
            throw new Error(payload.error || 'Saved events could not be updated.');
        }
        return payload;
    },

    removeLegacyCopy() {
        try {
            window.localStorage.removeItem(this.legacyStorageKey);
        } catch (error) {
            this.storageAvailable = false;
        }
    },

    async loadFromServer() {
        this.setButtonsDisabled(true);
        try {
            const payload = await this.request(this.apiUrl);
            const serverIds = this.normalizeIds(payload.event_ids);
            const localIds = [...this.ids];
            const idsToImport = localIds.filter((id) => !serverIds.includes(id));

            if (idsToImport.length) {
                const values = new URLSearchParams();
                idsToImport.forEach((id) => values.append('event_ids[]', id));
                try {
                    const imported = await this.request(this.importUrl, values);
                    this.ids = this.normalizeIds(imported.event_ids);
                    this.removeLegacyCopy();
                } catch (error) {
                    this.ids = [...new Set([...serverIds, ...localIds])];
                    this.persist();
                    window.showToast?.('Some saved events could not be synced to your account yet.', 'error');
                }
            } else {
                this.ids = [...new Set([...serverIds, ...localIds])];
                this.removeLegacyCopy();
            }
            this.persist();
            this.renderButtons();
            this.applyFilter();
        } catch (error) {
            window.showToast?.('Saved events could not be loaded from your account. Try again shortly.', 'error');
        } finally {
            this.setButtonsDisabled(false);
        }
    },

    async toggle(button) {
        if (button.disabled) return;
        const id = String(button.dataset.eventId);
        const title = button.dataset.eventTitle || 'Event';
        const savedIndex = this.ids.indexOf(id);
        const isSaving = savedIndex === -1;
        const previousIds = [...this.ids];

        if (isSaving) this.ids.push(id);
        else this.ids.splice(savedIndex, 1);

        this.renderButtons();
        this.applyFilter();

        if (!this.authenticated && !this.persist()) {
            this.ids = previousIds;
            this.renderButtons();
            this.applyFilter();
            window.showToast?.('Saved events could not be stored in this browser.', 'error');
            return;
        }

        if (this.authenticated) {
            this.setButtonsDisabled(true, id);
            try {
                const values = new URLSearchParams({
                    event_id: id,
                    saved: String(isSaving),
                });
                await this.request(this.apiUrl, values);
                this.persist();
                window.showToast?.(
                    isSaving ? `${title} saved to your account.` : `${title} removed from saved events.`,
                    isSaving ? 'success' : 'info'
                );
            } catch (error) {
                this.ids = previousIds;
                this.persist();
                this.renderButtons();
                this.applyFilter();
                window.showToast?.(error.message, 'error');
            } finally {
                this.setButtonsDisabled(false, id);
            }
            return;
        }

        window.showToast?.(
            isSaving ? `${title} saved for later.` : `${title} removed from saved events.`,
            isSaving ? 'success' : 'info'
        );
    },

    init() {
        const body = document.body;
        this.authenticated = body.dataset.authenticated === 'true';
        this.apiUrl = body.dataset.savedEventsUrl || '';
        this.importUrl = body.dataset.savedEventsImportUrl || '';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const userId = body.dataset.userId || '';
        this.storageKey = this.authenticated && userId
            ? `tukutane.savedEvents.v1.user.${userId}`
            : this.legacyStorageKey;
        this.ids = this.read();
        if (this.authenticated) {
            this.ids = [...new Set([...this.ids, ...this.read(this.legacyStorageKey)])];
        }
        this.renderButtons();
        this.applyFilter();

        document.addEventListener('click', (event) => {
            if (!(event.target instanceof Element)) return;
            const button = event.target.closest('[data-save-event]');
            if (button) void this.toggle(button);
        });

        document.querySelector('[data-saved-filter]')?.addEventListener('change', (event) => {
            const isSavedOnly = event.currentTarget.checked;
            this.applyFilter();
            this.updateSavedQuery(isSavedOnly);
        });

        window.addEventListener('storage', (event) => {
            if (event.key !== this.storageKey && event.key !== null) return;
            this.ids = this.read();
            this.renderButtons();
            this.applyFilter();
        });

        if (this.authenticated) void this.loadFromServer();
    },
};

const EventRegistration = {
    initSeatMeter() {
        const panel = document.querySelector('[data-seats-panel]');
        if (!panel) return;

        const capacity = Number(panel.dataset.capacity);
        const confirmed = Number(panel.dataset.confirmed);
        const remaining = Number(panel.dataset.remaining);
        if (![capacity, confirmed, remaining].every(Number.isFinite) || capacity < 0) return;

        const fill = panel.querySelector('[data-seats-fill]');
        const progress = panel.querySelector('[data-seats-progress]');
        const remainingOutput = panel.querySelector('[data-seats-remaining]');
        const confirmedOutput = panel.querySelector('[data-seats-confirmed]');
        const capacityOutput = panel.querySelector('[data-seats-capacity]');
        const status = document.querySelector('[data-registration-status]');
        const fillPercent = capacity > 0 ? Math.min(100, Math.round(confirmed / capacity * 100)) : 0;
        const seatsState = remaining <= 0
            ? 'error'
            : (capacity > 0 && remaining / capacity < 0.2 ? 'warning' : 'success');

        if (fill) fill.style.width = `${fillPercent}%`;
        if (progress) {
            progress.classList.remove('seats-progress-success', 'seats-progress-warning', 'seats-progress-error');
            progress.classList.add(`seats-progress-${seatsState}`);
            progress.setAttribute('aria-valuemax', String(capacity));
            progress.setAttribute('aria-valuenow', String(Math.min(capacity, confirmed)));
        }
        if (confirmedOutput) confirmedOutput.textContent = String(confirmed);
        if (remainingOutput) remainingOutput.textContent = String(remaining);
        if (capacityOutput) capacityOutput.textContent = String(capacity);
        if (status) {
            const registrationStatus = remaining > 0 ? 'confirmed' : 'waitlisted';
            status.className = `status status-${registrationStatus}`;
            status.textContent = registrationStatus === 'confirmed' ? 'Confirmed' : 'Waitlisted';
        }
    },

    initConfirmation() {
        const form = document.querySelector('[data-registration-form]');
        const dialog = document.querySelector('[data-registration-dialog]');
        if (!form || !dialog || typeof dialog.showModal !== 'function') return;

        let confirmedSubmit = false;
        form.addEventListener('submit', (event) => {
            if (confirmedSubmit) {
                confirmedSubmit = false;
                return;
            }
            event.preventDefault();
            dialog.showModal();
        });

        dialog.addEventListener('close', () => {
            if (dialog.returnValue !== 'confirm') return;
            confirmedSubmit = true;
            form.requestSubmit();
        });
    },

    init() {
        this.initSeatMeter();
        this.initConfirmation();
    },
};

document.addEventListener('DOMContentLoaded', () => {
    SavedEvents.init();
    EventRegistration.init();
});