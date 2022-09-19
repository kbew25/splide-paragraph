/**
 * @file
 * Carousel Class.
 */
/* global Splide */
(($, Drupal, Carousels) => {
  const mainDefaults = {
    type: 'loop',
    pagination: false,
    arrows: false,
    keyboard: false, // Splide listens to key events at the document level and moves ALL carousels when arrow keys are used. Since keyboard and screen reader users use these keys to operate the tabs, this creates a very unusual experience.
    slideFocus: false, // removes tabindex="0" from each slide wrapper, since we only want our links inside each slide to receive focus.
  };

  const navDefaults = {
    fixedWidth: 100,
    height: 100,
    gap: 10,
    rewind: true,
    arrows: false,
    pagination: false,
    isNavigation: true,
  };

  Carousels.Carousel = class {
    constructor($element, mainSettings, navSettings) {
      this.carousel = $element;
      this.mainSettings = Object.assign(mainDefaults, mainSettings);
      this.navSettings = Object.assign(navDefaults, navSettings);
      this.liveregion = $(
        '<div aria-live="polite" aria-atomic="true" class="live-region visually-hidden"></div>',
      );
    }

    /**
     * Build carousel.
     */
    init() {
      const _self = this;
      const $carousel = _self.carousel;
      const $main = $carousel.find('.carousel__main')[0];
      // Create carousel.
      const main = new Splide($main, _self.mainSettings);

      if ($carousel.find('.carousel__nav').length > 0) {
        const $nav = $carousel.find('.carousel__nav')[0];
        const nav = new Splide($nav, _self.navSettings);

        main.sync(nav);
        nav.mount();
      }

      main.mount();
      // Add a live region to announce the slide number.
      $carousel.prepend(_self.liveregion);
      // Add move functions.
      _self.onMove(main);
    }

    /**
     * On Move.
     *
     * @param {HTMLElement} carousel
     *   The carousel element.
     */
    onMove(carousel) {
      const _self = this;
      const $carousel = _self.carousel;
      const $previousButton = $carousel.find(
        '.carousel__controls__button--previous',
      );
      const $nextButton = $carousel.find('.carousel__controls__button--next');

      // To prevent animation issues, let's make every slide visible before a
      // transition happens. Splide will then automatically remove the `.
      // is-visible` class from non-visible slides once the transition is
      // finished.
      carousel.on('move', () => {
        const $slides = _self.carousel.find('.carousel__main .splide__slide');
        const slideLength = carousel.Components.Controller.getEnd() + 1;

        $slides.each((i, slide) => {
          $(slide).addClass('is-visible');
        });

        // Announce slide.
        _self.annouce(_self.liveregion, carousel.index, slideLength);
      });

      // Go to the previous slide when the Previous button is activated.
      $previousButton.on('click', () => {
        carousel.go('<');
      });

      // Go to the next slide when the Next button is activated.
      $nextButton.on('click', () => {
        carousel.go('>');
      });
    }

    /**
     * Announce text.
     *
     * @param {HTMLElement} $liveregion
     *   Liveregion element.
     * @param {number} index
     *   Slide index.
     * @param {number} length
     *   Slide length.
     */
    annouce($liveregion, index, length) {
      const _self = this; // eslint-disable-line no-unused-vars
      $liveregion.text(Drupal.t(`Slide (${index + 1}) of ${length}`));
    }
  };
})(jQuery, Drupal, (window.Carousels = window.Carousels || {}));
