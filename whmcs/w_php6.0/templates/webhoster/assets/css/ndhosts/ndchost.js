/* stop the main navbar from closing when content inside the megamenu is clicked.
opening and closing is handled by hover instead */
$(document).on('click', '.yamm .dropdown-menu', function(e) { e.stopPropagation() })
