function blank() {
  $(document).on('click', '.js-blank', function(e) {
    if ($('#blank').attr('rel', 'stylesheet')) {
      $('#blank').attr('rel', 'livewire');
    } else {
      $('#blank').attr('rel', 'stylesheet');
    }
    e.preventDefault();
  });
};
