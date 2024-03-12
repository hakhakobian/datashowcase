( function ( blocks, element ) {
  let el = element.createElement;
  let pluginData = tcfdatashowcase;
  blocks.registerBlockType( "tcfdatashowcase/view", {
    title: pluginData.title,
    category: 'common',
    attributes: {
      shortcode: {
        type: "string",
        value: ""
      },
      shortcode_id: {
        type: "int",
        value: 0
      }
    },

    edit: function ( props ) {
      // Create the shortcodes list and the container for preview.
      let cont = el( "div", {}, shortcodeList(), );

      return cont;

      /**
       * Create the format list html element.
       *
       * @returns {*}
       */
      function shortcodeList() {
        let shortcodes = JSON.parse( pluginData.data );

        // Add shortcodes to the html elements.
        let shortcode_list = [];
        shortcodes.forEach( function ( shortcode_data ) {
          shortcode_list.push(
            el( 'option', {
              value: shortcode_data.id,
              "data-shortcode": shortcode_data.shortcode,
            }, shortcode_data.title )
          );
        } );

        // Return the complete html list of formats.
        return el( 'select', {
          value: props.attributes.shortcode_id,
          onChange: itemSelect,
        }, shortcode_list );
      }

      /**
       * Bind an event on the item select.
       *
       * @param event
       */
      function itemSelect( event ) {
        let selected = event.target.querySelector( "option:checked" );
        // Get selected item's data.
        props.setAttributes( {
          shortcode: selected.dataset.shortcode,
          shortcode_id: selected.value,
        } );

        event.preventDefault();
      }
    },

    save: function ( props ) {
      return props.attributes.shortcode;
    }
  } );
} )(
  window.wp.blocks,
  window.wp.element
);