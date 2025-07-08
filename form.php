<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $utm_params = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    foreach ($utm_params as $param) {
        if (isset($_GET[$param])) {
            $_SESSION[$param] = $_GET[$param];
        }
    }

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Google reCAPTCHA keys should be kept in environment variables for better security

        $secretKey = "6Ldave8qAAAAAJBvxPfYm9HmLPB0s7BM0T8xFB0K";
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $userIP = $_SERVER['REMOTE_ADDR'];

        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaData = [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $userIP
        ];

        $recaptchaOptions = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($recaptchaData),
            ],
        ];
        $recaptchaContext = stream_context_create($recaptchaOptions);
        $recaptchaVerify = file_get_contents($recaptchaUrl, false, $recaptchaContext);
        $recaptchaSuccess = json_decode($recaptchaVerify);
        if (!$recaptchaSuccess->success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'reCAPTCHA verification failed. Please try again.']);
            exit();
        }


        if ($recaptchaSuccess->success) {
            $first_name = htmlspecialchars($_POST['first_name'] ?? '');
            $last_name = htmlspecialchars($_POST['last_name'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $mobile_number = htmlspecialchars($_POST['mobile_number'] ?? '');
            $srixCountryCode = htmlspecialchars($_POST['srixCountryCode'] ?? '');
            $project_name = htmlspecialchars($_POST['project_name'] ?? '');
            $preferred_language = htmlspecialchars($_POST['preferred_language'] ?? '');
            $detail_message = htmlspecialchars($_POST['detail_message'] ?? '');

            $inquiry_type = htmlspecialchars($_POST['inquiry_type'] ?? 'Inquiry from DPXPO for '.$_POST['project_name'] .' landing page Form');
            $inquiry = htmlspecialchars($_POST['inquiry'] ??  'Inquiry from DPXPO for '.$_POST['project_name'] .' landing page Form');
            $utm_source = $_SESSION['utm_source'] ?? null;
            $utm_medium = $_SESSION['utm_medium'] ?? null;
            $utm_campaign = $_SESSION['utm_campaign'] ?? null;
            $utm_term = $_SESSION['utm_term'] ?? null;
            $utm_content = $_SESSION['utm_content'] ?? null;
            $screen_width = $_POST['screen_width'] ?? '';
            $screen_height = $_POST['screen_height'] ?? '';

            // Validating inputs
            if (!$first_name || !$email || !$mobile_number) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid input detected. Please ensure all fields are filled correctly.']);
                exit();
            }

            $source =  'Inquiry from DPXPO for '.$_POST['project_name'] .' landing page Form';
            $data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $mobile_number,
                'inquiry' => $inquiry,
                'source' => $source,
                'profession' => $profession,
                'project' =>  $project_name,
                'utm_term' => 'Inhouse',
                'utm_medium' => 'HRE',
                'utm_source' => 'Property Showcase',
                'utm_campaign' => 'Road Show',
                'utm_content' => 'Landing Page - IND',
                'srixCountryCode' => $srixCountryCode,
                'CountryID' => '65946',
                'StateID' => '55367',
                'CityID' => '54788',
                'detail_message'=> $detail_message,
            ];

            $ch = curl_init('https://crm.harboruae.com/api/leadsGoyzer-2');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                $error_message = curl_error($ch);
                curl_close($ch);
                header('Content-Type: application/json');
                
                echo json_encode(['success' => false, 'message' => 'cURL error: ' . $error_message]);
                exit();
            }

            curl_close($ch);
            header('Content-Type: application/json');
            if ($http_status === 201) {
                echo json_encode(['success' => true, 'pdf_url' => './assets/HRE_Dubai_Real_Estate_Market_Rental_Report_2024_Eng.pdf']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error sending data to Laravel API. HTTP Status: ' . $http_status . '. Response: ' . $response]);
            }
            exit();
        }
    }
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="style/custom.css">
    <title>Document</title>
</head>

<body>
 <div id="TheFormWrapper" class="d-flex align-items-center py-4 ">

    <main id="TheForm" class="form-signin w-100 m-auto">
        <form id="enquireForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="text-center"><img class="mb-4" src="images/Harbor-Real-Estate-Logo.png" alt="" width="35%" height="auto"></div> 
            
            <div class="form-floating"> 
                <input type="text" class="form-control" id="floatingFirstName" placeholder="First Name" name="first_name" required> 
                <label for="floatingFirstName">First Name</label> 
            </div>
            <div class="form-floating"> 
                <input type="text" class="form-control" id="floatingLastName" placeholder="Last Name" name="last_name" required> 
                <label for="floatingLastName">Last Name</label> 
            </div>
            <div class="form-floating"> 
                <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com" name="email" required> 
                <label for="floatingEmail">Email address</label>
            </div>
            <div class="form-group"> 
                <input type="tel" class="form-control" id="mobile_number"
                                    placeholder="Mobile Number" name="mobile_number"
                                    title="Mobile number must be 10 digits long." required>
                                <input type="hidden" name="srixCountryCode" id="srixCountryCode">
                          
            </div>
              <div class="form-floating mt-4"> 
                <input type="text" class="form-control" id="floatingPreferredLanguage" placeholder="Preferred Language" name="preferred_language" required> 
                <label for="floatingPreferredLanguage">Preferred Language</label> 
            </div>

         
        <div class="form-group">
                                <select class="form-select" aria-label="Default select example" id="project_name"  name="project_name" required>
                                        <option >Select Interested Project</option>
                                        <option value="Skyhills Astra">Skyhills Astra</option>
                                        <option value="161 Jumeirah Lane">161 Jumeirah Lane</option>
                                        <option value="171 Garden Heights">171 Garden Heights</option>
                                        <option value="AA Tower">AA Tower</option>
                                </select>
                            </div>
                             <div class="form-floating mt-4">
                                  <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" name="detail_message"></textarea>
                                  <label for="floatingTextarea">Comments</label>
                            </div>
                            <div class="g-recaptcha" data-sitekey="6Ldave8qAAAAAPjGvtuLUMCM4UHumfqRyvttLWvg"></div>

            <script src="https://www.google.com/recaptcha/api.js?hl=en"></script>
                            <br>
                            <input type="hidden"
                                id="inquiryTypeInput" name="inquiry_type" value=""> <input type="hidden"
                                id="screenWidthInput" name="screen_width" value=""> <input type="hidden"
                                id="screenHeightInput" name="screen_height" value=""> 
          <button class="btn btn-danger w-100 py-2" type="submit">Submit</button>
          <div class="text-center"><img class="mt-4" src="images/Harbor-Real-Estate-Logo.png" alt="" width="35%" height="auto"></div> 
            <!-- <p class="mt-5 mb-3 text-body-secondary">© 2025</p> -->
        </form>
    </main>

</div>
 <script src="js/vendor/jquery.min.js"></script>
      
  

        <script src="js/submit-form.js"></script>
        <script src="js/vendor/isotope.pkgd.min.js"></script>
        <script src="js/video_embedded.js"></script>
        <script src="js/vendor/fslightbox.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK"
        crossorigin="anonymous"></script>
      

          <script>
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
                        // $("#theLoadingGif").show();

                    // Get formatted phone number in E164 format
                    // var fullPhoneNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
                    // $('#mobile_number').val(fullPhoneNumber);

                    // Capture screen dimensions and set them in hidden inputs
                    $('#screenWidthInput').val(window.innerWidth);
                    $('#screenHeightInput').val(window.innerHeight);

                    var formData = $(this).serialize();
                    var inquiryType = $('#inquiryTypeInput').val(); // Get the hidden input value
                        console.log(formData);
                    // Show loading spinner
                    // $('#loadingSpinner').show();

                    // Hide the modal immediately after form submission
                    // $('#enquireModal').modal('hide');

                    // Send conversion event to Google Ads
            

                    // Perform the AJAX request to submit the form
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: formData,
                        success: function(response) {
                          
                            
                            alert(xhr, status, erro);
                        
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
        </script>
</body>

</html>