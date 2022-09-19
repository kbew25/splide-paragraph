/**
 * @file
 * Carousel JS.
 */
(($, Drupal, drupalSettings, once, Carousels) => {
  /**
   * Carousel behaviours.
   */
  Drupal.behaviors.invMainCarousel = {
    /**
     * Attach function.
     *
     * @param {object} context
     *   Object containing the DOM.
     */
    attach(context) {
      $(
        once('carousel--init', '.carousel[data-carousel="carousel"]', context),
      ).each((i, ele) => {
        const $this = $(ele);
        const settings = drupalSettings.splide_paragraph.carouselSettings;
        const carousel = new Carousels.Carousel($this, settings);
        carousel.init();

        // On focus controls.
        if (drupalSettings.splide_paragraph.controlsType == 'focus') {
          $this.find('.carousel__controls button').on('focusin', () => {
            $this.find('.carousel__controls').removeClass('visually-hidden');
          });
          $this.find('.carousel__controls button').on('focusout', () => {
            $this.find('.carousel__controls').addClass('visually-hidden');
          });
        }
      });
    },
  };
})(jQuery, Drupal, drupalSettings, once, window.Carousels);
