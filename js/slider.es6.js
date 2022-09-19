/**
 * @file
 * Slider JS.
 */
(($, Drupal, drupalSettings, once, Carousels) => {
  /**
   * Slider behaviours.
   */
  Drupal.behaviors.invMainSlider = {
    /**
     * Attach function.
     *
     * @param {object} context
     *   Object containing the DOM.
     */
    attach(context) {
      $(
        once('carousel--init', '.carousel[data-carousel="slider"]', context),
      ).each((i, ele) => {
        const settings = drupalSettings.splide_paragraph.carouselSettings;
        const carousel = new Carousels.Slider($(ele), settings);
        carousel.init();
      });
    },
  };
})(jQuery, Drupal, drupalSettings, once, window.Carousels);
