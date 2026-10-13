import Choices from 'choices.js';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

function initControls(scope = document) {
  scope.querySelectorAll('[data-enhanced-select]').forEach((select) => {
    if (select._kenangaChoices) return;
    select._kenangaChoices = new Choices(select, {
      searchEnabled: true,
      searchPlaceholderValue: 'Cari pilihan…',
      itemSelectText: '',
      shouldSort: false,
      allowHTML: false,
      noResultsText: 'Tidak ada hasil',
      noChoicesText: 'Tidak ada pilihan',
      placeholder: true,
      placeholderValue: select.dataset.placeholder || 'Pilih pilihan',
    });
  });

  scope.querySelectorAll('[data-date-picker]').forEach((input) => {
    if (input._flatpickr) return;
    const picker = flatpickr(input, {
      locale: Indonesian,
      dateFormat: 'Y-m-d',
      altInput: true,
      altInputClass: 'input kenanga-date-display',
      altFormat: 'j F Y',
      allowInput: false,
      disableMobile: true,
    });
    if (picker.altInput && input.id) {
      picker.altInput.id = input.id;
      input.id += '-value';
    }
  });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => initControls());
else initControls();
window.EnhancedControls = { init: initControls };
