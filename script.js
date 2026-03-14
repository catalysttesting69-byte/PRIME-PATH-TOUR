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

    const trips = [
        {
            title: '6 Days 5 Nights Zanzibar Discovery Tour Package',
            url: 'trip-details.html',
            image: 'https://images.unsplash.com/photo-1590523741831-ab7e8b8f9c7f?w=640&h=420&fit=crop',
            price: '$1,250', priceNum: 1250,
            priceLabel: 'from',
            highlights: ['Stone Town & Spice Farms', 'Mnemba Snorkeling & Turtles'],
            route: [{ name: '5 nights Zanzibar', multi: true }],
            type: 'zanzibar'
        },
        {
            title: '7 Days 6 Nights Zanzibar Discovery Experience',
            url: 'trip-details-7-days.html',
            image: 'https://images.unsplash.com/photo-1544551763-8dd44758c2dd?w=640&h=420&fit=crop',
            price: '$1,450', priceNum: 1450,
            priceLabel: 'from',
            highlights: ['Nakupenda & Prison Island', 'Safari Blue Experience'],
            route: [{ name: '6 nights Zanzibar', multi: true }],
            type: 'zanzibar'
        },
        {
            title: '8-Day Tanzania Great Migration Safari',
            url: 'trip-details-8-days.html',
            image: 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=640&h=420&fit=crop',
            price: '$3,950', priceNum: 3950,
            priceLabel: 'from',
            highlights: ['Ngorongoro Crater', 'Great Wildebeest Migration Spectacle'],
            route: ['Arusha', 'Tarangire', { name: '3 days Serengeti', multi: true }, 'Ngorongoro'],
            type: 'safari'
        },
        {
            title: '10-Day Tanzania Big Five & Cultural Safari',
            url: 'trip-details-10-days.html',
            image: 'https://images.unsplash.com/photo-1549366021-9f761d450615?w=640&h=420&fit=crop',
            price: '$4,500', priceNum: 4500,
            priceLabel: 'from',
            highlights: ['Mto wa Mbu Village', 'Tarangire & Serengeti'],
            route: ['Arusha', 'Lake Manyara', { name: '3 days Serengeti', multi: true }, 'Ngorongoro', { name: '2 days Tarangire', multi: true }],
            type: 'safari'
        },
        {
            title: '12 Days Tanzania Safari & Zanzibar Beach Holiday Escape',
            url: 'trip-details-12-days.html',
            image: 'https://images.unsplash.com/photo-1540541338287-41700207dee6?w=640&h=420&fit=crop',
            price: '$5,250', priceNum: 5250,
            priceLabel: 'from',
            highlights: ['Romantic Honeymoon', 'Serengeti & Zanzibar'],
            route: ['Arusha', 'Tarangire', 'Lake Manyara', { name: '3 days Serengeti', multi: true }, 'Ngorongoro', { name: '5 days Zanzibar', multi: true }],
            type: 'combined'
        }
    ];

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

    const excursions = [
        {
            name: 'Stone Town Tour',
            image: 'https://images.unsplash.com/photo-1621245089855-87bd754f9d68?w=600&h=400&fit=crop',
            category: 'City Tour',
            price: '$35',
            desc: "Explore the winding alleys, historical sites, and vibrant markets of Zanzibar's most historic city."
        },
        {
            name: 'Prison Island',
            image: 'https://images.unsplash.com/photo-1550064434-6c3e6dc27cc5?w=600&h=400&fit=crop',
            category: 'Island Trip',
            price: '$45',
            desc: 'Take a boat ride to see the giant Aldabra tortoises and relax on pristine white sand beaches.'
        },
        {
            name: 'Jozani Forest',
            image: 'https://images.unsplash.com/photo-1540569876033-6e43130d2238?w=600&h=400&fit=crop',
            category: 'Nature',
            price: '$40',
            desc: 'Walk through lush landscapes and spot the rare red colobus monkeys in their natural habitat.'
        },
        {
            name: 'Masingini Forest',
            image: 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=600&h=400&fit=crop',
            category: 'Nature',
            price: '$30',
            desc: 'Discover hidden trails and diverse wildlife in this ancient, peaceful tropical forest reserve.'
        },
        {
            name: 'Kuza Cave',
            image: 'https://images.unsplash.com/photo-1546944062-878f24458f23?w=600&h=400&fit=crop',
            category: 'Adventure',
            price: '$25',
            desc: 'Swim in the crystal-clear, mineral-rich healing waters of this ancient sacred limestone cave.'
        },
        {
            name: 'Turtle Aquarium',
            image: 'https://images.unsplash.com/photo-1437622368342-7a3d73a34c8f?w=600&h=400&fit=crop',
            category: 'Marine',
            price: '$35',
            desc: 'Feed and swim with rescued sea turtles in a natural lagoon, a truly heartwarming experience.'
        },
        {
            name: 'Horse riding',
            image: 'https://images.unsplash.com/photo-1533036496924-4fbea4078dd2?w=600&h=400&fit=crop',
            category: 'Adventure',
            price: '$60',
            desc: 'Enjoy a magical sunset ride along the pristine white beaches on majestic, well-trained horses.'
        },
        {
            name: 'Quad Biking',
            image: 'https://images.unsplash.com/photo-1571401314352-7e5fca067c29?w=600&h=400&fit=crop',
            category: 'Adventure',
            price: '$75',
            desc: 'Embark on an adrenaline-filled off-road adventure through remote villages and rugged landscapes.'
        }
    ];



    // ===== RENDER TRIPS =====
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

    renderTrips(trips);

    // Filter pills
    document.querySelectorAll('.type-pill').forEach(pill => {
        pill.addEventListener('click', () => {
            const filterType = pill.dataset.filter;

            // If excursions filter, scroll to excursions section
            if (filterType === 'excursions') {
                const excursionsSection = document.getElementById('excursions');
                if (excursionsSection) {
                    excursionsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                document.querySelectorAll('.type-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                return;
            }

            document.querySelectorAll('.type-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeFilter = filterType;
            renderTrips(getFilteredSorted());
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
    const excursionsGrid = document.getElementById('excursionsGrid');
    if (excursionsGrid) {
        excursionsGrid.innerHTML = excursions.map((e, i) => `
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

    document.getElementById('enquiryForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('mName')?.value.trim();
        const email = document.getElementById('mEmail')?.value.trim();
        if (!name || !email) {
            alert('Please fill in your name and email.');
            return;
        }
        const form = document.getElementById('enquiryForm');
        const success = document.getElementById('modalSuccess');
        if (form) form.style.display = 'none';
        if (success) success.style.display = 'flex';
    });

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
});
