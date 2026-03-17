/* ================================================
   TANZANIA SPECIALIST – Zanzibar Holiday Clone
   script.js
   ================================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ===== HERO SLIDER =====
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    let current = 0;
    let slideTimer;

    function goToSlide(index) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function startSlider() {
        slideTimer = setInterval(() => goToSlide(current + 1), 5500);
    }

    if (slides.length) {
        startSlider();
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                clearInterval(slideTimer);
                goToSlide(parseInt(dot.dataset.slide));
                startSlider();
            });
        });
    }


    // ===== NAVBAR HIDE/SHOW ON SCROLL =====
    const mainHeader = document.getElementById('mainHeader');
    const heroSection = document.querySelector('.hero-new');
    let lastScrollTop = 0;
    let isNavbarHidden = false;

    window.addEventListener('scroll', () => {
        let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        // Get the hero section height (approximately 100vh or minimum 700px)
        const heroHeight = heroSection ? heroSection.offsetHeight : window.innerHeight;

        // If scrolled past hero section
        if (currentScroll > heroHeight) {
            // Scrolling down - hide navbar
            if (currentScroll > lastScrollTop && !isNavbarHidden) {
                mainHeader.classList.add('navbar-hidden');
                isNavbarHidden = true;
            }
            // Scrolling up - show navbar
            else if (currentScroll < lastScrollTop && isNavbarHidden) {
                mainHeader.classList.remove('navbar-hidden');
                isNavbarHidden = false;
            }
        } else {
            // Always show navbar in hero section
            mainHeader.classList.remove('navbar-hidden');
            isNavbarHidden = false;
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    }, false);


    // ===== DATA =====
    let observer; // declared early so renderTrips can safely reference it

    let trips = [];
    let excursionsData = [];

    async function fetchTrips() {
        try {
            const response = await fetch('api/get_all_trips.php');
            const data = await response.json();
            if (data.success) {
                // Separate packages (trips) from excursions
                const allData = data.trips;
                trips = allData.filter(t => t.type !== 'excursions');
                
                excursionsData = allData.filter(t => t.type === 'excursions').map(t => ({
                    name: t.title,
                    image: t.image,
                    category: t.highlights && t.highlights.length ? t.highlights[0] : 'General',
                    price: t.price,
                    desc: t.description || ''
                }));
                
                renderTrips(getFilteredSorted());
                updatePillCounts();
                renderExcursionsGrid();
            }
        } catch (err) {
            console.error("Error fetching trips:", err);
        }
    }

    function updatePillCounts() {
        const counts = {
            all: trips.length,
            zanzibar: trips.filter(t => t.type === 'zanzibar').length,
            safari: trips.filter(t => t.type === 'safari').length,
            combined: trips.filter(t => t.type === 'combined').length
        };

        document.querySelectorAll('.type-pill').forEach(pill => {
            const filter = pill.dataset.filter;
            if (filter === 'excursions') return; // Skip excursions pill for counts
            
            const countSpan = pill.querySelector('.pill-count');
            if (countSpan && counts[filter] !== undefined) {
                countSpan.textContent = counts[filter];
            }
        });
    }

    const lodges = [
        {
            name: 'Legendary Zanzibar Beach Resort',
            image: 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=500&h=360&fit=crop',
            url: '#',
            badge: 'Accommodation'
        },
        {
            name: 'Qambani Luxury Resort',
            image: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=500&h=360&fit=crop',
            url: '#',
            badge: 'Accommodation'
        },
        {
            name: 'Fun Beach Resort',
            image: 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=500&h=360&fit=crop',
            url: '#',
            badge: 'Accommodation'
        },
        {
            name: 'Lux* Marijani',
            image: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500&h=360&fit=crop',
            url: '#',
            badge: 'Accommodation'
        }
    ];

    // Old excursions array removed - now using excursionsData merged into trips

    const reviews = [
        {
            name: "Sarah Jenkins",
            location: "London, UK",
            source: "google",
            rating: 5,
            quote: "An absolute dream! Our 10-day safari was perfectly organized. Our guide, Joseph, was incredible at spotting the Big 5. Highly recommend PrimePath!",
            date: "2 weeks ago"
        },
        {
            name: "Markus Weber",
            location: "Munich, Germany",
            source: "tripadvisor",
            rating: 5,
            quote: "Zanzibar part of the trip was pure bliss. The Nungwi beach is stunning. PrimePath's local knowledge really shows. Everything was seamless.",
            date: "1 month ago"
        },
        {
            name: "Elena Rodriguez",
            location: "Madrid, Spain",
            source: "google",
            rating: 5,
            quote: "Stone Town tour was fascinating. The spice farm visit was also a highlight for our kids. Very professional and friendly staff.",
            date: "3 days ago"
        },
        {
            name: "David Thompson",
            location: "New York, USA",
            source: "tripadvisor",
            rating: 4,
            quote: "Serengeti was breath-taking. The Great Migration is something everyone should see once. Accommodations exceeded our expectations.",
            date: "2 months ago"
        },
        {
            name: "Sophie Bennett",
            location: "Sydney, Australia",
            source: "google",
            rating: 5,
            quote: "Best vacation ever. We loved the mixture of adventure and relaxation. The horses at the beach ride was a magical experience at sunset.",
            date: "1 week ago"
        },
        {
            name: "Jan De Vries",
            location: "Amsterdam, Netherlands",
            source: "tripadvisor",
            rating: 5,
            quote: "Fantastische ervaring! De gidsen zijn erg deskundig. We hebben zoveel geleerd over de lokale cultuur en natuur. Bedankt PrimePath!",
            date: "3 weeks ago"
        },
        {
            name: "Claire Dubois",
            location: "Paris, France",
            source: "google",
            rating: 5,
            quote: "Une expérience inoubliable. Le cratère du Ngorongoro est un paradis sur terre. Le service était irréprochable du début à la fin.",
            date: "5 days ago"
        },
        {
            name: "James Wilson",
            location: "Toronto, Canada",
            source: "tripadvisor",
            rating: 5,
            quote: "Prison Island and the giant tortoises were amazing. Perfect for a family outing. PrimePath handles all the logistics so you can just enjoy.",
            date: "1 month ago"
        },
        {
            name: "Ami Suzuki",
            location: "Tokyo, Japan",
            source: "google",
            rating: 5,
            quote: "The snorkeling trip to Mnemba Atoll was like swimming in an aquarium. So many colorful fish and even saw dolphins! Amazing day.",
            date: "2 weeks ago"
        },
        {
            name: "Robert Miller",
            location: "Cape Town, SA",
            source: "google",
            rating: 4,
            quote: "Great value for money. The 6-day discovery package covered all the essentials. Efficient communication and great local guides.",
            date: "1 month ago"
        },
        {
            name: "Lisa Anderson",
            location: "Stockholm, Sweden",
            source: "tripadvisor",
            rating: 5,
            quote: "We spent our honeymoon with PrimePath and couldn't be happier. They added so many special touches that made it extra romantic.",
            date: "2 months ago"
        },
        {
            name: "Thomas Mueller",
            location: "Berlin, Germany",
            source: "google",
            rating: 5,
            quote: "Super Organisation! Alles hat perfekt geklappt. Die Transfers waren pünktlich und die Lodges waren einfach traumhaft.",
            date: "3 weeks ago"
        }
    ];



    // ===== RENDER TRIPS FUNCTION =====
    const tripContainer = document.getElementById('tripContainer');
    const emptyMsg = document.getElementById('tripsEmptyMsg');

    function renderTrips(data) {
        if (!tripContainer) return;
        if (!data.length) {
            tripContainer.innerHTML = '';
            if (emptyMsg) emptyMsg.style.display = 'block';
            return;
        }
        if (emptyMsg) emptyMsg.style.display = 'none';
        tripContainer.innerHTML = data.map((t, i) => {
            const daysMatch = t.title.match(/^(\d+)\s+Days?/i);
            const days = daysMatch ? daysMatch[1] : '';
            return `
      <article class="trip-card reveal" style="transition-delay:${i * .05}s">
        <div class="trip-card-image">
          <img src="${t.image}" alt="${t.title}" loading="lazy">
        </div>
        <div class="trip-card-body">
          <div class="trip-card-meta">
            ${days ? `<span class="trip-meta-item"><i class="far fa-clock"></i> ${days} Days</span>` : ''}
            <span class="trip-meta-item trip-price-new">
              <small>${t.priceLabel}</small> ${t.price} <small>p.p.</small>
            </span>
          </div>
          <h3><a href="${t.url}">${t.title}</a></h3>
          <ul class="trip-card-features">
            ${t.highlights.map(h => `<li><i class="fas fa-check"></i> ${h}</li>`).join('')}
          </ul>
          <div class="trip-route-simple">
            <i class="fas fa-map-marker-alt"></i>
            <span>${t.route.map(r => typeof r === 'object' ? r.name.replace(/^\d+\s+days\s+/i, '') : r).join(' - ')}</span>
          </div>
        </div>
        <div class="trip-card-footer">
          <a href="${t.url}" class="btn-primary">View Trip</a>
          <button class="btn-outline" data-open-enquiry>Enquire</button>
        </div>
      </article>
    `;
        }).join('');
        // re-observe new cards after filter/sort re-renders
        if (observer) {
            document.querySelectorAll('.trip-card.reveal:not(.visible)').forEach(el => observer.observe(el));
        }
    }

    // ===== SCROLL REVEAL ANIMATIONS =====
    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { root: null, rootMargin: '0px 0px -60px 0px', threshold: 0.12 });

    document.querySelectorAll('.fade-up, .reveal').forEach(el => observer.observe(el));

    // Initialize data
    fetchTrips();

    let activeFilter = 'all';

    function getFilteredSorted() {
        let data = activeFilter === 'all' ? [...trips] : trips.filter(t => t.type === activeFilter);
        const activeOption = document.querySelector('.sort-options li.active');
        const sort = activeOption?.dataset.value || 'default';
        if (sort === 'price-asc') data.sort((a, b) => a.priceNum - b.priceNum);
        else if (sort === 'price-desc') data.sort((a, b) => b.priceNum - a.priceNum);
        else if (sort === 'duration') data.sort((a, b) => {
            const dA = parseInt(a.title.match(/^(\d+)/)?.[1] || 0);
            const dB = parseInt(b.title.match(/^(\d+)/)?.[1] || 0);
            return dA - dB;
        });
        return data;
    }

    // Filter pills
    document.querySelectorAll('.type-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            const filterType = pill.dataset.filter;

            if (filterType === 'excursions') {
                const excursionsSection = document.getElementById('excursions');
                if (excursionsSection) {
                    excursionsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                return;
            }

            document.querySelectorAll('.type-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeFilter = filterType;
            renderTrips(getFilteredSorted());

            const tripsSection = document.getElementById('tripsSection');
            if (tripsSection) {
                tripsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Custom Sort Dropdown
    const sortDropdown = document.getElementById('sortDropdown');
    const sortTrigger = document.getElementById('sortTrigger');
    const sortOptions = document.querySelectorAll('.sort-options li');
    const sortCurrent = document.getElementById('sortCurrent');

    if (sortTrigger && sortDropdown) {
        sortTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            sortDropdown.classList.toggle('open');
        });

        sortOptions.forEach(opt => {
            opt.addEventListener('click', () => {
                sortOptions.forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                sortCurrent.textContent = opt.textContent;
                sortDropdown.classList.remove('open');
                renderTrips(getFilteredSorted());
            });
        });

        document.addEventListener('click', () => {
            sortDropdown.classList.remove('open');
        });
    }

    // Global reset filter (used by empty message link)
    window.resetFilter = function () {
        activeFilter = 'all';
        document.querySelectorAll('.type-pill').forEach(p => p.classList.remove('active'));
        document.querySelector('.type-pill[data-filter="all"]')?.classList.add('active');
        renderTrips(trips);
    };

    // ===== RENDER EXCURSIONS =====
    function renderExcursionsGrid() {
        const excursionsGrid = document.getElementById('excursionsGrid');
        if (excursionsGrid) {
            excursionsGrid.innerHTML = excursionsData.map((e, i) => `
                <div class="excursion-card reveal" style="transition-delay:${i * .06}s">
                    <div class="excursion-card-image">
                        <img src="${e.image}" alt="${e.name}" loading="lazy">
                        <span class="excursion-badge">${e.category}</span>
                    </div>
                    <div class="excursion-card-body">
                        <div class="excursion-meta">
                            <span class="excursion-price">from <strong>${e.price}</strong></span>
                        </div>
                        <h3>${e.name}</h3>
                        <p class="excursion-desc">${e.desc}</p>
                        <div class="excursion-footer">
                            <button class="excursion-enquire-btn" data-open-enquiry>Enquire <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Re-observe the new excursion cards
            if (observer) {
                document.querySelectorAll('.excursion-card.reveal:not(.visible)').forEach(el => observer.observe(el));
            }
        }
    }

    // ===== RENDER REVIEWS (SLIDING) =====
    const reviewsGrid = document.getElementById('reviewsGrid');
    if (reviewsGrid) {
        // Shuffle reviews for variety
        let shuffled = [...reviews].sort(() => 0.5 - Math.random());
        
        // Render all shuffled reviews into the flex track
        reviewsGrid.innerHTML = shuffled.map((r, i) => `
            <div class="review-card">
                <div class="review-source-icon">
                    <img src="${r.source === 'google' ? 'https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg' : 'https://www.vectorlogo.zone/logos/tripadvisor/tripadvisor-icon.svg'}" alt="${r.source}">
                </div>
                <div class="review-stars">
                    ${Array(r.rating).fill('<i class="fa-solid fa-star"></i>').join('')}${Array(5 - r.rating).fill('<i class="fa-regular fa-star" style="opacity:0.3"></i>').join('')}
                </div>
                <div class="review-quote">"${r.quote}"</div>
                <div class="review-author">
                    <img src="https://i.pravatar.cc/100?u=${encodeURIComponent(r.name)}" alt="${r.name}" class="review-avatar">
                    <div class="review-info">
                        <span class="review-name">${r.name}</span>
                        <span class="review-meta">${r.location} • ${r.date}</span>
                    </div>
                </div>
            </div>
        `).join('');

        let reviewIndex = 0;
        const totalReviews = shuffled.length;
        
        function slideReviews() {
            // How many items are visible? (CSS: Desktop 3, Tablet 2, Mobile 1)
            const visibleCount = window.innerWidth > 992 ? 3 : (window.innerWidth > 600 ? 2 : 1);
            
            // Increment index
            reviewIndex++;
            
            // If we've reached the end of possible slides
            if (reviewIndex > totalReviews - visibleCount) {
                reviewIndex = 0; // Loop back
            }
            
            // Calculate movement percentage correctly based on flex layout
            const gap = 25; // gap: 25px in CSS
            const containerWidth = reviewsGrid.parentElement.offsetWidth;
            const itemWidth = (containerWidth - (gap * (visibleCount - 1))) / visibleCount;
            const moveAmount = reviewIndex * (itemWidth + gap);
            
            reviewsGrid.style.transform = `translateX(-${moveAmount}px)`;
        }

        // Start auto-slide every 5 seconds
        let reviewTimer = setInterval(slideReviews, 5000);

        // Optional: Pause on hover
        reviewsGrid.addEventListener('mouseenter', () => clearInterval(reviewTimer));
        reviewsGrid.addEventListener('mouseleave', () => reviewTimer = setInterval(slideReviews, 5000));
        
        // Handle window resize to avoid broken offsets
        window.addEventListener('resize', () => {
             reviewIndex = 0;
             reviewsGrid.style.transform = `translateX(0)`;
        });
    }


    // Custom Hero Dropdown
    const heroTripDropdown = document.getElementById('heroTripTypeDropdown');
    const heroTripTrigger = document.getElementById('heroTripTypeTrigger');
    const heroTripOptions = document.querySelectorAll('#heroTripTypeOptions li');
    const heroTripCurrent = document.getElementById('heroTripTypeCurrent');

    if (heroTripTrigger && heroTripDropdown) {
        heroTripTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            heroTripDropdown.classList.toggle('open');
        });

        heroTripOptions.forEach(opt => {
            opt.addEventListener('click', () => {
                heroTripOptions.forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                heroTripCurrent.textContent = opt.textContent;
                heroTripDropdown.classList.remove('open');
            });
        });

        document.addEventListener('click', () => {
            heroTripDropdown?.classList.remove('open');
        });
    }

    // Hero Number Inputs (Plus/Minus)
    document.querySelectorAll('.num-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const inputId = btn.dataset.id;
            const input = document.getElementById(inputId);
            if (!input) return;

            let val = parseInt(input.value) || 0;
            if (btn.classList.contains('plus')) {
                val++;
            } else {
                val = Math.max(parseInt(input.min) || 0, val - 1);
            }
            input.value = val;
        });
    });

    // ===== DATE PICKER FUNCTIONALITY =====
    const datePickerContainer = document.querySelector('.hero-date-picker');
    const dateInput = document.getElementById('heroArrival');

    if (datePickerContainer && dateInput) {
        // Allow clicking the entire container/icon to open the picker natively
        datePickerContainer.addEventListener('click', () => {
            try {
                dateInput.showPicker();
            } catch (err) {
                dateInput.focus();
            }
        });

        dateInput.addEventListener('click', function (e) {
            // Prevent double triggering if container was also clicked
            e.stopPropagation();
            try {
                this.showPicker();
            } catch (err) {
                this.focus();
            }
        });
    }

    // ===== ENQUIRY MODAL =====
    const modal = document.getElementById('enquiryModal');

    function openModal() {
        if (!modal) return;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
        // Reset to form view
        const form = document.getElementById('enquiryForm');
        const success = document.getElementById('modalSuccess');
        if (form) { form.style.display = ''; form.reset(); }
        if (success) success.style.display = 'none';
    }

    // Open triggers — hero form button + all data-open-enquiry elements (delegated)
    document.getElementById('heroFormSubmit')?.addEventListener('click', () => {
        // Transfer data from hero form to modal
        const heroTrip = document.getElementById('heroTripTypeCurrent')?.textContent;
        const heroAdults = document.getElementById('heroAdults')?.value;
        const heroKids = document.getElementById('heroChildren')?.value;

        const mTrip = document.getElementById('mTripType');
        if (mTrip && heroTrip) {
            // Match the option text or value
            for (let i = 0; i < mTrip.options.length; i++) {
                if (mTrip.options[i].text === heroTrip) {
                    mTrip.selectedIndex = i;
                    break;
                }
            }
        }
        if (document.getElementById('mAdults')) document.getElementById('mAdults').value = heroAdults;
        if (document.getElementById('mChildren')) document.getElementById('mChildren').value = heroKids;

        openModal();
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-open-enquiry]')) {
            e.preventDefault();
            openModal();
        }
    });

    document.getElementById('modalBackdrop')?.addEventListener('click', closeModal);
    document.getElementById('modalClose')?.addEventListener('click', closeModal);
    document.getElementById('modalCloseSuccess')?.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    // NOTE: The enquiry modal submit handler lives in the inline <script> on index.html.
    // It sends the form data to /api/book.php via fetch() and shows #modalSuccess on success.
    // DO NOT add a second handler here — duplicate handlers cause a conflict.

    // ===== MOBILE NAV TOGGLE =====
    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');

    if (mobileToggle && navLinks) {
        // Toggle menu on hamburger click
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileToggle.classList.toggle('active');
            navLinks.classList.toggle('open');
            document.body.classList.toggle('menu-open');
        });

        // Close menu when clicking on nav links
        const navItems = navLinks.querySelectorAll('.nav-item a');
        navItems.forEach(link => {
            link.addEventListener('click', () => {
                mobileToggle.classList.remove('active');
                navLinks.classList.remove('open');
                document.body.classList.remove('menu-open');
            });
        });

        // Close menu when clicking on backdrop (body overlay)
        document.addEventListener('click', (e) => {
            if (navLinks.classList.contains('open') &&
                !navLinks.contains(e.target) &&
                !mobileToggle.contains(e.target)) {
                mobileToggle.classList.remove('active');
                navLinks.classList.remove('open');
                document.body.classList.remove('menu-open');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navLinks.classList.contains('open')) {
                mobileToggle.classList.remove('active');
                navLinks.classList.remove('open');
                document.body.classList.remove('menu-open');
            }
        });
    }



    // ===== FLOATING BUTTONS VISIBILITY =====
    const floatingWa = document.getElementById('floatingWhatsApp');
    window.addEventListener('scroll', () => {
        const show = window.scrollY > 600;
        floatingWa?.classList.toggle('visible', show);
    }, { passive: true });

    // ===== SCROLL REVEAL ANIMATIONS =====
    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { root: null, rootMargin: '0px 0px -60px 0px', threshold: 0.12 });

    document.querySelectorAll('.fade-up, .reveal').forEach(el => observer.observe(el));


    // ===== GLOBAL NEWSLETTER SUBSCRIBE =====
    // Uses class selectors so it works on every page (index, trip-details, about, etc.)
    // The newsletter form in each footer just needs .newsletter-input and .newsletter-btn classes.
    (function () {
        'use strict';
        const emailInput = document.querySelector('.newsletter-input');
        const btn        = document.querySelector('.newsletter-btn');
        const msg        = document.getElementById('newsletterMsg');

        if (!btn || !emailInput) return;

        btn.addEventListener('click', async function () {
            const email = emailInput.value.trim();
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                if (msg) { msg.style.color = '#dc3545'; msg.textContent = 'Please enter a valid email address.'; }
                else { alert('Please enter a valid email address.'); }
                return;
            }

            btn.disabled = true;
            btn.textContent = '...';
            if (msg) msg.textContent = '';

            try {
                const res  = await fetch('api/subscribe.php', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify({ email }),
                });
                const data = await res.json();
                if (msg) {
                    msg.style.color = data.success ? '#2d6a4f' : '#dc3545';
                    msg.textContent = data.message;
                } else {
                    alert(data.message);
                }
                if (data.success) emailInput.value = '';
            } catch (e) {
                const errMsg = 'Connection error. Please try again.';
                if (msg) { msg.style.color = '#dc3545'; msg.textContent = errMsg; }
                else { alert(errMsg); }
            } finally {
                btn.disabled = false;
                btn.textContent = 'Subscribe';
            }
        });
    })();
});
