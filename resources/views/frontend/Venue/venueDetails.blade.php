@extends('frontend.layout')
@section('pageHeading')

@endsection





@section('custom-style')
<link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
@endsection

@section('hero-section')

<!-- Page Banner Start -->

<!-- Page Banner End -->

<!-- Event Page Start -->
<section class="event-details-section pt-110 rpt-90 pb-90 rpb-70">
  <div class="container">
    <div class="event-details-content">
      <div class="event-top d-flex flex-wrap-wrap has-gap">
        <div class="event-top-date">
          <div class="event-month">
            Jan</div>
          <div class="event-date">
            17</div>
        </div>
        <div class="event-bottom-content">
          <div class="event-details-top">
            <h2 class="title">{{$data->name}} <span class="badge badge-info">Upcoming</span>
            </h2>
          </div>

          <div class="event-details-header mb-25">
            <ul>
              <li><i class="far fa-calendar-alt"></i>
                Sat, 17th Jan 2026
              </li>

              <li><i class="far fa-clock"></i>
                12h
              </li>
              <li><i class="fas fa-map-marker-alt"></i>
                {{$data->location}}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="event-details-image mb-50">
        <div class="event-details-images">
          <a href="https://event.apnademand.com/assets/admin/img/event-gallery/6457429c32842.jpg"><img class="lazy" data-src="https://event.apnademand.com/assets/admin/img/event-gallery/6457429c32842.jpg" alt="Event Details"></a>
          <a href="https://event.apnademand.com/assets/admin/img/event-gallery/6457429c3a965.jpg"><img class="lazy" data-src="https://event.apnademand.com/assets/admin/img/event-gallery/6457429c3a965.jpg" alt="Event Details"></a>
        </div>

        <div class="buttons">
          <a href="javascript:void(0)" data-toggle="modal" data-target=".bd-example-modal-lg">
            <i class="fas fa-map-marker-alt m-0"></i>
          </a>
          <a href="https://event.apnademand.com/addto/wishlist/105" class=""><i class="fas fa-bookmark"></i></a>
          <a href="javascript:void(0)" data-toggle="modal" data-target=".share-event">
            <i class="fas fa-share-alt"></i></a>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-7">
          <div class="event-details-content-inner">
            <div class="event-info d-flex align-items-center mb-1">
              <span>
                <a href="https://event.apnademand.com/events?category=wedding">Wedding</a>
              </span>
            </div>
            <h3 class="inner-title mb-25">Description</h3>

            <div class="summernote-content">
              <p>{{$data->description}}</p>
            </div>

            <h3 class="inner-title mb-30">Map</h3>
            <div class="our-location mb-50">
              <iframe src="//maps.google.com/maps?width=100%25&amp;height=385&amp;hl=en&amp;q=Main St  Readsboro  United States&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" height="385" class="map-h" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <h3>Return Policy</h3>
            <p>{{$data->cancellation_policy}}</p>

          </div>
        </div>
        <div class="col-lg-5">
          <div class="sidebar-sticky">
          
    
              <div class="event-details-information">
            


                


                <b>Organised By</b>
                <hr>
                <div class="author">
                  <a href="https://event.apnademand.com/organizer/details/23/organizer">
                    <img class="lazy" data-src="https://event.apnademand.com/assets/admin/img/organizer-photo/6457819a4ad93.png" alt="Author">

                  </a>

                  <div class="content">
                    <h6><a href="https://event.apnademand.com/organizer/details/23/organizer">organizer</a>
                    </h6>
                    <a href="https://event.apnademand.com/organizer/details/23/organizer">{{$data->namne}}</a>
                  </div>
                </div>
                <b><i class="fas fa-map-marker-alt"></i>{{$data->location}}</b>
                <hr>






                <b>Select Booking</b>
                <hr>
                <form action="{{route('venues.details1.store')}}" method="post">
                  @csrf

                  <div>
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" name="start_date" required>
                  </div>


                  <div>
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" name="end_date" required>
                  </div>

                  <div>
                    <label for="startTime">Start Time:</label>
                    <input type="time" id="startTime" name="start_time" required>
                  </div>

                  <div>
                    <label for="endTime">End Time:</label>
                    <input type="time" id="endTime" name="end_time" required>
                  </div>


                  <div>
    <label>Guest Type:</label>
    <div>
        <input type="checkbox" id="vegGuest" name="guests" value="veg" onchange="toggleDropdown('vegDropdown')">
        <label for="vegGuest">Veg Guest</label>
    </div>
    <div id="vegDropdown" class="hidden-dropdown">
        <label for="vegCount">Number of Veg Guests:</label>
        <input type="number" id="vegCount" name="veg_guests" min="1">
    </div>

    <div>
        <input type="checkbox" id="nonVegGuest" name="guests" value="non-veg" onchange="toggleDropdown('nonVegDropdown')">
        <label for="nonVegGuest">Non-Veg Guest</label>
    </div>
    <div id="nonVegDropdown" class="hidden-dropdown">
        <label for="nonVegCount">Number of Non-Veg Guests:</label>
        <input type="number" id="nonVegCount" name="non_veg_guests" min="1">
    </div>
</div>

                  <!-- <div>
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                      <option value="confirmed">Confirmed</option>
                      <option value="pending">Pending</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div> -->


                  <input type="hidden" name="venue_id" value="{{$data->id}}" readonly>




                  <div>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                  </div>


                  <div>
                    <label for="payment">Payment:</label>
                    <input type="text" id="payment" name="payment" value="500" readonly>
                  </div>
                  <div>
                    <label for="payment">Coupon Code:</label>
                    <input type="text" id="payment" name="coupon"  >
                  </div>


                  </span>



              </div>
              <button class="theme-btn w-100 mt-20" type="submit">Book Now</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <div class="text-center mt-4">
      <a href="http://example.com/" target="_blank" onclick="adView(14)">
        <img data-src="https://event.apnademand.com/assets/admin/img/advertisements/64577dfdeb19e.png" src="images/64577dfdeb19e.png" class="lazy" alt="advertisement" style="width: 728px; max-height: 90px;max-width: 100%;">
      </a>
    </div>
  </div>
  <hr>
  <div class="releted-event-header mt-50">
    <h3>Related Events</h3>
    <div class="slick-next-prev mb-10">
      <button class="prev"><i class="fas fa-chevron-left"></i></button>
      <button class="next"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>
  <div class="related-event-wrap">
    <div class="event-item">
      <div class="event-image">
        <a href="https://event.apnademand.com/event/grand-night-party/103">
          <img class="lazy" data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683438918.png" alt="Event">
        </a>
      </div>
      <div class="event-content">
        <ul class="time-info">
          <li>
            <i class="far fa-calendar-alt"></i>
            <span>
              27 Jan
            </span>
          </li>
          <li>
            <i class="far fa-hourglass"></i>
            <span title="Event Duration">2h 11m</span>
          </li>
          <li>
            <i class="far fa-clock"></i>
            <span>
              09:00 AM
            </span>
          </li>
        </ul>
        <h5>
          <a href="https://event.apnademand.com/event/grand-night-party/103">
            Grand night party
          </a>
        </h5>

        <p>{{$data->dscription}}</p>

        <div class="price-remain">
          <div class="location">
            <i class="fas fa-map-marker-alt"></i>
            <span>
              {{$data->location}}
            </span>
          </div>
          <span>
            <span class="price" dir="ltr">


              ₹95
              <span>
                <del>
                  ₹100
                </del>
              </span>
              <strong>*</strong>
            </span>
          </span>
        </div>
      </div>
      <a href="https://event.apnademand.com/addto/wishlist/103" class="wishlist-btn ">
        <i class="far fa-bookmark"></i>
      </a>
    </div>
    <div class="event-item">
      <div class="event-image">
        <a href="https://event.apnademand.com/event/decoration-of-the-marriage/91">
          <img class="lazy" data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683370360.png" alt="Event">
        </a>
      </div>
      <div class="event-content">
        <ul class="time-info">
          <li>
            <i class="far fa-calendar-alt"></i>
            <span>
              18 Jan
            </span>
          </li>
          <li>
            <i class="far fa-hourglass"></i>
            <span title="Event Duration">2d 1m</span>
          </li>
          <li>
            <i class="far fa-clock"></i>
            <span>
              04:00 PM
            </span>
          </li>
        </ul>
        <a href="https://event.apnademand.com/organizer/details/25/ambrose" class="organizer">By�&nbsp;�&nbsp;ambrose</a>
        <h5>
          <a href="https://event.apnademand.com/event/decoration-of-the-marriage/91">
            Decoration of the marriage
          </a>
        </h5>

        <p>{{$data->description}}</p>

        <div class="price-remain">
          <div class="location">
            <i class="fas fa-map-marker-alt"></i>
            <span>
              {{$data->location}}
            </span>
          </div>
          <span>
            <span class="price" dir="ltr">

              {{$data->price}}
              <strong></strong>
            </span>
          </span>
        </div>
      </div>
      <a href="https://event.apnademand.com/addto/wishlist/91" class="wishlist-btn ">
        <i class="far fa-bookmark"></i>
      </a>
    </div>
  </div>

  </div>
</section>
<!-- Event Page End -->


<div data-popup_delay="2000" data-popup_id="8" id="modal-popup-8" class="popup-wrapper">
  <div class="popup-one bg_cover lazy" data-bg="https://event.apnademand.com/assets/admin/img/popups/64577ac23d6b5.png">
    <div class="popup_main-content" style="background-color: #2079FF; opacity: 0.80;">
      <h1>ENJOY 10% OFF</h1>
      <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>
      <a href="https://event.apnademand.com" class="popup-main-btn" style="background-color: #2079FF;">Book Now</a>
    </div>
  </div>
</div>


<footer class="footer-section bg-lighter pt-100" style="background:#011444">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-lg-5 col-sm-6">
        <div class="footer-widget about-widget">
          <div class="footer-logo mb-30">
            <a href="https://event.apnademand.com"><img src="images/1721131458.png" alt="Logo"></a>
          </div>
          <p></p>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus dignissimos quibusdam quia sit delectus. Cupiditate corporis, delectus quo ullam repudiandae illum culpa, magni modi, asperiores quis non magnam fugit vitae!</p>
          <p></p>
          <div class="social-style-one mt-30">
            <a href="https://www.facebook.com/eventapnademand/"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.linkedin.com/company/eventapnademand/about/?viewAsMember=true"><i class="fab fa-linkedin-in"></i></a>
            <a href="https://twitter.com/"><i class="fab fa-twitter"></i></a>
            <a href="http://www.apnademand.com/"><i class="fab fa-amilia"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="footer-widget link-widget ml-sm-auto">
          <h5 class="footer-title">Quick Links</h5>
          <ul>
            <li><a href="https://codecanyon8.kreativdev.com/evento/terms-&amp;-conditions">Terms &amp; Conditions</a></li>
            <li><a href="https://codecanyon8.kreativdev.com/evento/contact">Contact Us</a></li>
            <li><a href="https://codecanyon8.kreativdev.com/evento/about">About Us</a></li>
            <li><a href="https://codecanyon8.kreativdev.com/evento/organizers">Organizers</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="footer-widget about-widget ml-sm-auto">
          <h5 class="footer-title">Contact Us</h5>
          <p class="ip">
            <i class="fas fa-map-marker-alt"></i>
            Electronic Market, Gola Rd, near Near Co-Operative Bank, Bakarganj, Patna, Bihar 800004
          </p>

          <p class="ip">
            <i class="fas fa-envelope"></i>
            <a href="mailto:support@event.apnademand.com" class="d-inline-block text-transform-normal">support@event.apnademand.com</a>
          </p>

          <p class="ip"><i class="fas fa-mobile-alt"></i>
            <a href="tel:18001020650">18001020650</a>
          </p>
        </div>
      </div>
    </div>

    <div class="copyright-area">
      <p></p>
      <p>Copyright ©2024. All Rights Reserved. Apnademand�&nbsp;</p>
      <p></p>
      <!-- Scroll Top Button -->
      <button class="scroll-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></button>
    </div>
  </div>
</footer>

</div>
<!--End pagewrapper-->


<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content p-2">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>

      <iframe src="//maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=Main St  Readsboro  United States&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" height="380" style="border:0; width: 100%;max-height:600px" allowfullscreen="" loading="lazy"></iframe>


    </div>
  </div>
</div>

<div class="modal fade share-event" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Share Now</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="button-group">
          <a href="//www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fevent.apnademand.com%2Fevent%2Fdesigner-carrier-conference%2F105" target="_blank">
            <i class="fab fa-facebook-f"></i>
            <p>Facebook</p>
          </a>
          <a href="//twitter.com/intent/tweet?text=my share text&amp;url=https%3A%2F%2Fevent.apnademand.com%2Fevent%2Fdesigner-carrier-conference%2F105" target="_blank"><i class="fab fa-twitter"></i>
            <p>Twitter</p>
          </a>
          <a href="//www.linkedin.com/shareArticle?mini=true&amp;url=https%3A%2F%2Fevent.apnademand.com%2Fevent%2Fdesigner-carrier-conference%2F105&amp;title=" target="_blank"><i class="fab fa-linkedin"></i>
            <p>linkedin</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  "use strict";
  var rtl = 0;
</script>
<!-- Jquery -->
<script>
  var baseUrl = "https://event.apnademand.com";
</script>
<script src="js/jquery.min.js"></script>
<!-- Popper -->
<script src="js/popper.min.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.4.5.3.min.js"></script>
<!-- jQuery UI js -->
<script src="js/jquery-ui.min.js"></script>
<!-- Isotope -->
<script src="js/isotope.pkgd.min.js"></script>
<!-- Magific Popup -->
<script src="js/jquery.magnific-popup.min.js"></script>
<!-- Image Loaded -->
<script src="js/imagesloaded.pkgd.min.js"></script>
<!-- Slick Slider -->
<script src="js/slick.min.js"></script>
<!-- Main JS -->
<script src="js/vanilla-lazyload.min.js"></script>
<script src="js/jquery-syotimer.min.js"></script>
<script src="js/datatables.min.js"></script>
<script src="js/script.js"></script>
<script src="js/event.js"></script>
<script src="js/toastr.js"></script>
<script src="js/cart.js"></script>
<script src="js/pwa.js" defer=""></script>

<script>
</script>




<div class="cookie">
  <div class="js-cookie-consent cookie-consent">
    <div class="container">
      <div class="cookie-container">
        <span class="cookie-consent__message">
          <p>We use cookies to give you the best online experience.<br>By continuing to browse the site you are agreeing to our use of cookies.</p>
        </span>

        <button class="js-cookie-consent-agree cookie-consent__agree">
          I Agree
        </button>
      </div>
    </div>
  </div>

  <script>
    "use strict";
    window.laravelCookieConsent = (function() {
      const COOKIE_VALUE = 1;
      const COOKIE_DOMAIN = 'event.apnademand.com';

      function consentWithCookies() {
        setCookie('laravel_cookie_consent', COOKIE_VALUE,
          7300);
        hideCookieDialog();
      }

      function cookieExists(name) {
        return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
      }

      function hideCookieDialog() {
        const dialogs = document.getElementsByClassName('js-cookie-consent');

        for (let i = 0; i < dialogs.length; ++i) {
          dialogs[i].style.display = 'none';
        }
      }

      function setCookie(name, value, expirationInDays) {
        const date = new Date();
        date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value +
          ';expires=' + date.toUTCString() +
          ';domain=' + COOKIE_DOMAIN +
          ';path=/' +
          '';
      }

      if (cookieExists('laravel_cookie_consent')) {
        hideCookieDialog();
      }

      const buttons = document.getElementsByClassName('js-cookie-consent-agree');

      for (let i = 0; i < buttons.length; ++i) {
        buttons[i].addEventListener('click', consentWithCookies);
      }

      return {
        consentWithCookies: consentWithCookies,
        hideCookieDialog: hideCookieDialog
      };
    })();
  </script>
</div>

<style>
        .hidden-dropdown {
            display: none;
        }
    </style>
    <script>
        function toggleDropdown(dropdownId) {
            var dropdown = document.getElementById(dropdownId);
            var checkbox = document.getElementById(dropdownId.replace('Dropdown', 'Guest'));

            if (checkbox.checked) {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }
    </script>




@endsection
@section('modals')
@includeIf('frontend.partials.modals')
@endsection