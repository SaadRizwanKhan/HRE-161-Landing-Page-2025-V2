 $(document).ready(function() {
                $('#inquiryTypeInput').val('enquire');

                // Handle modal opening and inquiry type
                $('[data-toggle="modal"]').on('click', function(e) {
                    e.preventDefault();
                    $('#enquireModal').modal('show');

                    var source = $(this).data('source');
                    if (!source) {
                        source = 'enquire';
                    }
                    $('#inquiryTypeInput').val(source);

                    $('#registrationNote').css('display', source === 'brochure' ? 'block' : 'none');
                });

                // Initialize intlTelInput for phone number formatting
                var input = document.querySelector("#mobile_number");
                var iti = window.intlTelInput(input, {
                    preferredCountries: ['ae'],
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js" // Required for validation and formatting
                });
                document.getElementById('srixCountryCode').value = iti.getSelectedCountryData().dialCode;
                // Handle form submission
                $('#enquireForm').on('submit', function(e) {
                    e.preventDefault();
                        console.log("hello");

                        //start the loader:
                        $("#theLoadingGif").show();

                    // Get formatted phone number in E164 format
                    // var fullPhoneNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
                    // $('#mobile_number').val(fullPhoneNumber);

                    // Capture screen dimensions and set them in hidden inputs
                    $('#screenWidthInput').val(window.innerWidth);
                    $('#screenHeightInput').val(window.innerHeight);

                    var formData = $(this).serialize();
                    var inquiryType = $('#inquiryTypeInput').val(); // Get the hidden input value

                    // Show loading spinner
                    $('#loadingSpinner').show();

                    // Hide the modal immediately after form submission
                    // $('#enquireModal').modal('hide');

                    // Send conversion event to Google Ads
            

                    // Perform the AJAX request to submit the form
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: formData,
                        success: function(response) {
                           $("#theLoadingGif").hide();
                            
                            alert('Our team will contact you. Thank you for your interest!');
                        
                        },
                        error: function(xhr, status, error) {
                            $('#loadingSpinner').hide();
                            alert('An error occurred while submitting the form.');
                            $('#enquireModal').modal('show');
                        }    
                    });
                });

                // Hide the loading spinner initially
                $('#loadingSpinner').hide();
            });

            // WhatsApp click tracking
            const whatsappElement = document.getElementById('whatsapp-click');
            if (whatsappElement) {
                whatsappElement.addEventListener('click', function(event) {
                    trackWhatsAppClick();
                });
            }   

            // Function to open image modal
            function openModal(img) {
                var modal = document.getElementById("zoomModal");
                var modalImg = document.getElementById("modalImage");
                modal.style.display = "block";
                modalImg.src = img.src;
                document.body.style.overflow = "hidden";
            }

            // Function to close the modal
            function closeModal(event) {
                var modal = document.getElementById("zoomModal");
                var modalImg = document.getElementById("modalImage");

                if (event.target !== modalImg) {
                    modal.style.display = "none"; // Close the modal
                    document.body.style.overflow = "auto";
                }
            }

            // Open modal if URL contains #contact
            if (window.location.hash === "#contact") {
                var modal = new bootstrap.Modal(document.getElementById('enquireModal'));
                modal.show();
            }

            // Close modal on scroll
            function closeModalOnScroll() {
                var modal = document.getElementById("zoomModal");
                modal.style.display = "none"; // Close the modal
                document.body.style.overflow = "auto";
            }

            $('#sliderWrapper').slick({
  centerPadding: '10px',
  slidesToShow: 3,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 1
      }
    }
  ]
});