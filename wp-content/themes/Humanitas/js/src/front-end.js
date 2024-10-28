/* eslint-disable max-len, no-param-reassign, no-unused-vars */
/**
 * Air theme JavaScript.
 */

// Import modules
import reframe from 'reframe.js';
import {
  styleExternalLinks,
  initExternalLinkLabels,
} from './modules/external-link';
import initAnchors from './modules/anchors';
import backToTop from './modules/top';
import initA11ySkipLink from './modules/a11y-skip-link';
import initA11yFocusSearchField from './modules/a11y-focus-search-field';
import customLanguageSwitcher from './modules/custom-language-switcher';
import wcagSettings from './modules/wcag-settings';
import offerSearch from './modules/offer-search';
import onScrollAnimation from './modules/on-scroll-animation';
import iterativeDelay from './modules/iterative-delay';
import sliders from './modules/sliders';
import blockOffer from './modules/block-offer';
import archiveFilters from './modules/archive-filters';
import secondaryNavigation from './modules/secondary-navigation';
import stickyNavigation from './modules/sticky-navigation';
import megaMenu from './modules/mega-menu';

// Define Javascript is active by changing the body class
document.body.classList.remove('no-js');
document.body.classList.add('js');

document.addEventListener('DOMContentLoaded', () => {
  initAnchors();
  backToTop();
  styleExternalLinks();
  initExternalLinkLabels();
  initA11ySkipLink();
  initA11yFocusSearchField();

  customLanguageSwitcher();
  archiveFilters();
  wcagSettings();
  offerSearch();
  onScrollAnimation();
  iterativeDelay();
  sliders();
  blockOffer();
  secondaryNavigation();
  stickyNavigation();
  megaMenu();

  // Fit video embeds to container
  reframe('.wp-has-aspect-ratio iframe');
});
