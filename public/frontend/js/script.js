// Smooth Scroll Utility for target buttons
const scrollButtons = document.querySelectorAll("[data-scroll-target]");

scrollButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const target = document.querySelector(button.dataset.scrollTarget);
    if (target) {
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
});

// Mobile Navigation Drawer Toggle Logic
const navToggle = document.querySelector(".nav-toggle");
const navCollapse = document.querySelector(".navbar-collapse");
const navLinks = document.querySelectorAll(".nav-link");
const navActionButtons = document.querySelectorAll(".navbar-collapse button");

if (navToggle && navCollapse) {
  const toggleMenu = () => {
    const isOpen = navCollapse.classList.contains("show");
    if (isOpen) {
      navCollapse.classList.remove("show");
      navToggle.setAttribute("aria-expanded", "false");
    } else {
      navCollapse.classList.add("show");
      navToggle.setAttribute("aria-expanded", "true");
    }
  };

  navToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    toggleMenu();
  });

  const closeMenu = () => {
    navCollapse.classList.remove("show");
    navToggle.setAttribute("aria-expanded", "false");
  };

  // Close drawer on clicking individual links or header actions
  navLinks.forEach((link) => {
    link.addEventListener("click", closeMenu);
  });

  navActionButtons.forEach((button) => {
    button.addEventListener("click", closeMenu);
  });

  // Close drawer on clicking outside the drawer
  document.addEventListener("click", (e) => {
    const clickInsideDrawer = navCollapse.contains(e.target);
    const clickInsideToggle = navToggle.contains(e.target);
    if (!clickInsideDrawer && !clickInsideToggle && navCollapse.classList.contains("show")) {
      navCollapse.classList.remove("show");
      navToggle.setAttribute("aria-expanded", "false");
    }
  });
}

// Newsletter Form Submit Interaction
const newsletterForm = document.querySelector(".newsletter-form");

if (newsletterForm) {
  newsletterForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const email = newsletterForm.querySelector("input[type='email']");
    const button = newsletterForm.querySelector("button");

    if (!email.value.trim()) {
      return;
    }

    button.textContent = "Joined!";
    button.disabled = true;
    email.value = "";
  });
}

// Featured Reads Slider Control Logic
const articleSlider = document.querySelector("[data-article-slider]");
const previousSlide = document.querySelector("[data-slider-prev]");
const nextSlide = document.querySelector("[data-slider-next]");

if (articleSlider && previousSlide && nextSlide) {
  const scrollSlider = (direction) => {
    const card = articleSlider.querySelector(".feature-card");
    // Slider scroll distance: card width + grid gap (28px)
    const distance = card ? card.getBoundingClientRect().width + 28 : 380;

    articleSlider.scrollBy({
      left: direction * distance,
      behavior: "smooth",
    });
  };

  previousSlide.addEventListener("click", () => scrollSlider(-1));
  nextSlide.addEventListener("click", () => scrollSlider(1));
}


// Reusable Mobile Carousel Controls
const mobileCarouselButtons = document.querySelectorAll('[data-carousel-target][data-carousel-direction]');

mobileCarouselButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const carousel = document.querySelector(button.dataset.carouselTarget);
    if (!carousel) return;

    const firstItem = carousel.children[0];
    const gap = Number.parseFloat(getComputedStyle(carousel).columnGap || getComputedStyle(carousel).gap) || 16;
    const distance = firstItem ? firstItem.getBoundingClientRect().width + gap : carousel.clientWidth * 0.85;
    const direction = Number(button.dataset.carouselDirection || 1);

    carousel.scrollBy({
      left: direction * distance,
      behavior: 'smooth',
    });
  });
});

// Interactive Topic Tab Navigation Active State Toggling
const topicButtons = document.querySelectorAll(".topic-tabs button");

if (topicButtons.length > 0) {
  topicButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      topicButtons.forEach((tab) => tab.classList.remove("active"));
      btn.classList.add("active");
    });
  });
}


// Article Search + Main/Sub Category Filtering
const articleSearch = document.querySelector('[data-article-search]');
const mainFilterButtons = document.querySelectorAll('[data-main-filter]');
const subFilterButtons = document.querySelectorAll('[data-sub-filter]');
const articleCards = document.querySelectorAll('.article-card[data-sub-category]');
const resultCount = document.querySelector('[data-result-count]');
const emptyState = document.querySelector('[data-empty-state]');
const articleSort = document.querySelector('[data-article-sort]');
const articleGroups = document.querySelectorAll('[data-article-group]');

if (articleCards.length > 0) {
  const articleParams = new URLSearchParams(window.location.search);
  let activeMainFilter = articleParams.get('main') || 'all';
  let activeSubFilter = articleParams.get('sub') || articleParams.get('category') || 'all';
  const initialArticleSearch = articleParams.get('q') || articleParams.get('search') || '';

  if (articleSearch && initialArticleSearch) {
    articleSearch.value = initialArticleSearch;
  }

  const syncFilterButtons = () => {
    mainFilterButtons.forEach((button) => {
      button.classList.toggle('active', (button.dataset.mainFilter || 'all') === activeMainFilter);
    });

    subFilterButtons.forEach((button) => {
      button.classList.toggle('active', (button.dataset.subFilter || 'all') === activeSubFilter);
    });
  };

  const updateArticles = () => {
    const query = articleSearch ? articleSearch.value.trim().toLowerCase() : '';
    let visibleCount = 0;

    articleCards.forEach((card) => {
      const mainCategory = card.dataset.mainCategory || '';
      const subCategory = card.dataset.subCategory || card.dataset.category || '';
      const title = card.dataset.title?.toLowerCase() || '';
      const cardText = card.textContent.toLowerCase();
      const searchableText = `${title} ${cardText} ${mainCategory} ${subCategory}`;
      const matchesMain = activeMainFilter === 'all' || mainCategory === activeMainFilter;
      const matchesSub = activeSubFilter === 'all' || subCategory === activeSubFilter;
      const matchesSearch = !query || searchableText.includes(query);
      const isVisible = matchesMain && matchesSub && matchesSearch;

      card.classList.toggle('is-filter-hidden', !isVisible);
      if (isVisible) visibleCount += 1;
    });

    if (articleSort) {
      const visibleCards = [...articleCards].filter((card) => !card.classList.contains('is-filter-hidden'));
      const sortedCards = visibleCards.sort((a, b) => {
        const mode = articleSort.value;
        if (mode === 'popular') return Number(b.dataset.views || 0) - Number(a.dataset.views || 0);
        if (mode === 'quick') return Number(a.dataset.readTime || 99) - Number(b.dataset.readTime || 99);
        if (mode === 'az') return (a.dataset.title || '').localeCompare(b.dataset.title || '');
        return new Date(b.dataset.date || 0) - new Date(a.dataset.date || 0);
      });

      sortedCards.forEach((card) => {
        const grid = card.closest('.all-posts-grid') || card.parentElement;
        if (grid) grid.appendChild(card);
      });
    }

    articleGroups.forEach((group) => {
      const hasVisibleCard = [...group.querySelectorAll('.article-card')].some((card) => !card.classList.contains('is-filter-hidden'));
      group.classList.toggle('is-hidden', !hasVisibleCard);
    });

    if (resultCount) {
      resultCount.textContent = `Showing ${visibleCount} article${visibleCount === 1 ? '' : 's'}`;
    }

    if (emptyState) {
      emptyState.classList.toggle('show', visibleCount === 0);
    }

    // Reset scroll position on homepage latest blogs carousel and toggle navigation controls visibility
    const carouselGrid = document.querySelector('.home-page #latest .article-grid');
    if (carouselGrid) {
      carouselGrid.scrollLeft = 0;
    }
    const latestControls = document.querySelector('[data-latest-carousel-controls]');
    if (latestControls) {
      latestControls.style.setProperty('display', visibleCount <= 1 ? 'none' : '', 'important');
    }

    if (window.makoonsPagination?.refresh) {
      window.makoonsPagination.refresh('articles', true);
    }
  };

  mainFilterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeMainFilter = button.dataset.mainFilter || 'all';
      mainFilterButtons.forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
      updateArticles();
    });
  });

  subFilterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeSubFilter = button.dataset.subFilter || 'all';
      subFilterButtons.forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
      updateArticles();
    });
  });

  if (articleSearch) {
    articleSearch.addEventListener('input', updateArticles);
  }

  if (articleSort) {
    articleSort.addEventListener('change', updateArticles);
  }

  syncFilterButtons();
  updateArticles();
}


// Article detail page advanced interactions
const progressBar = document.querySelector('[data-reading-progress]');
const articleContent = document.querySelector('[data-article-content]');
const copyButtons = document.querySelectorAll('[data-copy-link]');
const printButton = document.querySelector('[data-print-page]');
const fontIncrease = document.querySelector('[data-font-increase]');
const fontDecrease = document.querySelector('[data-font-decrease]');

if (progressBar && articleContent) {
  const updateProgress = () => {
    const rect = articleContent.getBoundingClientRect();
    const scrollable = articleContent.offsetHeight - window.innerHeight;
    const progress = Math.min(100, Math.max(0, ((-rect.top + 120) / scrollable) * 100));
    progressBar.style.width = `${Number.isFinite(progress) ? progress : 0}%`;
  };

  window.addEventListener('scroll', updateProgress, { passive: true });
  window.addEventListener('resize', updateProgress);
  updateProgress();
}

if (copyButtons.length > 0) {
  copyButtons.forEach((button) => {
    button.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        const original = button.textContent;
        button.classList.add('copied');
        if (button.textContent.trim()) {
          button.textContent = 'Copied';
        }
        setTimeout(() => {
          button.classList.remove('copied');
          if (original.trim()) {
            button.textContent = original;
          }
        }, 1500);
      } catch (error) {
        button.textContent = 'Copy failed';
      }
    });
  });
}

if (printButton) {
  printButton.addEventListener('click', () => window.print());
}

if (articleContent && fontIncrease && fontDecrease) {
  let articleFontSize = 1.08;
  const applyFontSize = () => {
    articleContent.style.setProperty('--article-font-size', `${articleFontSize}rem`);
  };

  fontIncrease.addEventListener('click', () => {
    articleFontSize = Math.min(1.24, articleFontSize + 0.04);
    applyFontSize();
  });

  fontDecrease.addEventListener('click', () => {
    articleFontSize = Math.max(0.96, articleFontSize - 0.04);
    applyFontSize();
  });
}


// Header Search Overlay
const searchOpenButtons = document.querySelectorAll('[data-search-open]');
const searchOverlay = document.querySelector('[data-search-overlay]');
const searchCloseButtons = document.querySelectorAll('[data-search-close]');
const globalSearchInput = document.querySelector('[data-global-search-input]');
const globalSearchItems = document.querySelectorAll('[data-search-item]');
const globalSearchEmpty = document.querySelector('[data-global-search-empty]');

if (searchOverlay && globalSearchInput) {
  const setSearchOpen = (isOpen) => {
    searchOverlay.classList.toggle('is-open', isOpen);
    searchOverlay.setAttribute('aria-hidden', String(!isOpen));
    document.body.classList.toggle('search-open', isOpen);
    searchOpenButtons.forEach((button) => button.setAttribute('aria-expanded', String(isOpen)));

    if (isOpen) {
      setTimeout(() => globalSearchInput.focus(), 80);
    }
  };

  const updateGlobalSearch = () => {
    const query = globalSearchInput.value.trim().toLowerCase();
    let visible = 0;

    globalSearchItems.forEach((item) => {
      const title = item.dataset.title?.toLowerCase() || item.textContent.toLowerCase();
      const category = item.dataset.category?.toLowerCase() || '';
      const matched = !query || title.includes(query) || category.includes(query);
      item.classList.toggle('is-hidden', !matched);
      if (matched) visible += 1;
    });

    if (globalSearchEmpty) {
      globalSearchEmpty.classList.toggle('show', visible === 0);
    }
  };

  searchOpenButtons.forEach((button) => {
    button.addEventListener('click', () => setSearchOpen(true));
  });

  searchCloseButtons.forEach((button) => {
    button.addEventListener('click', () => setSearchOpen(false));
  });

  globalSearchInput.addEventListener('input', updateGlobalSearch);

  globalSearchInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      const firstVisible = [...globalSearchItems].find((item) => !item.classList.contains('is-hidden'));
      if (firstVisible) {
        window.location.href = firstVisible.href;
      }
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && searchOverlay.classList.contains('is-open')) {
      setSearchOpen(false);
    }
  });

  updateGlobalSearch();
}

// Author profile routing for clickable author chips
const authorLinks = document.querySelectorAll('.post-author');

if (authorLinks.length > 0) {
  const authorPages = {
    'sana kapoor': 'author-sana-kapoor.html'
  };

  authorLinks.forEach((link) => {
    const name = link.textContent.trim().toLowerCase();
    if (!link.getAttribute('href') || link.getAttribute('href') === '#') {
      const authorSlug = name.replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
      link.setAttribute('href', authorPages[name] || `author.html?author=${authorSlug}`);
    }
  });
}

// Generic author page content
const authorDirectoryPage = document.querySelector('[data-author-page]');

if (authorDirectoryPage) {
  const authorProfiles = {
    'mira-sharma': {
      name: 'Mira Sharma',
      image: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=260&q=86',
      topic: 'Preschool',
      bio: 'Mira writes about first weeks at preschool, classroom confidence, and the tiny routines that help children feel known.',
      posts: [
        { title: 'A gentle guide to your child’s first week at preschool', category: 'Preschool · 5 min read', excerpt: 'Small routines that help children feel calm, known, and ready to join the classroom.', date: 'June 12, 2026', views: '2.4k views', media: 'media-one' },
        { title: 'How teachers notice confidence before children can explain it', category: 'Preschool · 5 min read', excerpt: 'A close look at small classroom signs: eye contact, quiet participation, and returning to favorite corners.', date: 'May 10, 2026', views: '604 views', media: 'media-four' }
      ]
    },
    'aarav-mehta': {
      name: 'Aarav Mehta',
      image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=260&q=86',
      topic: 'Daycare',
      bio: 'Aarav focuses on daycare routines, emotional safety, and how predictable rhythms make long days easier for young children.',
      posts: [
        { title: 'How thoughtful daycare routines help children feel secure', category: 'Daycare · 4 min read', excerpt: 'Why repeated rhythms make the day easier for children and more predictable for parents.', date: 'June 10, 2026', views: '1.8k views', media: 'media-two' },
        { title: 'Nap time without pressure: what helps children rest', category: 'Daycare · 5 min read', excerpt: 'Gentle cues, familiar objects, and calm transitions that make rest time feel safe.', date: 'May 22, 2026', views: '814 views', media: 'media-five' }
      ]
    },
    'sana-kapoor': {
      name: 'Sana Kapoor',
      image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=260&q=86',
      topic: 'Food',
      bio: 'Sana writes about food, comfort routines, and the small practical choices that help preschool children settle into school life with more ease.',
      posts: [
        { title: 'Simple lunchbox ideas for the first month of school', category: 'Food · 3 min read', excerpt: 'Familiar, easy-to-eat foods that support comfort while children settle into a new space.', date: 'June 4, 2026', views: '1.5k views', media: 'media-three' },
        { title: 'Familiar food and calmer preschool mornings', category: 'Food · 4 min read', excerpt: 'Why predictable breakfast and snack choices can support smoother drop-offs.', date: 'May 20, 2026', views: '1.4k views', media: 'media-two' },
        { title: 'What to pack when your child is still settling', category: 'Daycare · 4 min read', excerpt: 'A soft checklist for comfort objects, food, water, and simple school-day preparedness.', date: 'April 18, 2026', views: '1.2k views', media: 'media-one' }
      ]
    },
    'riya-sen': {
      name: 'Riya Sen',
      image: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=260&q=86',
      topic: 'Play',
      bio: 'Riya writes about play, repetition, creativity, and the quiet learning that happens when children return to the same idea again and again.',
      posts: [
        { title: 'What children learn when they repeat the same game', category: 'Play · 4 min read', excerpt: 'A calmer way to understand repetition, confidence, imagination, and early problem solving.', date: 'May 28, 2026', views: '1.2k views', media: 'media-four' },
        { title: 'When messy play becomes language', category: 'Activities · 4 min read', excerpt: 'How paint, clay, and pretend work help children tell stories before they have all the words.', date: 'May 16, 2026', views: '732 views', media: 'media-five' },
        { title: 'How children build friendships through repeated play', category: 'Parenting · 6 min read', excerpt: 'Why shared games and predictable roles can make new friendships feel less overwhelming.', date: 'April 26, 2026', views: '690 views', media: 'media-two' }
      ]
    },
    'neha-batra': {
      name: 'Neha Batra',
      image: 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=260&q=86',
      topic: 'Parenting',
      bio: 'Neha writes practical notes for parents on independence, goodbyes, confidence, and simple family rhythms.',
      posts: [
        { title: 'What to say when your child says they do not want to go', category: 'Parenting · 4 min read', excerpt: 'Soft language that validates feelings while keeping the school routine steady.', date: 'June 8, 2026', views: '1.2k views', media: 'media-five' },
        { title: 'Helping children say goodbye without rushing feelings', category: 'Parenting · 4 min read', excerpt: 'A simple routine that keeps goodbyes warm, short, and trustworthy.', date: 'May 18, 2026', views: '946 views', media: 'media-one' }
      ]
    },
    'kabir-anand': {
      name: 'Kabir Anand',
      image: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=260&q=86',
      topic: 'Learning',
      bio: 'Kabir writes about early problem solving, home learning, and how children practice ideas through ordinary play.',
      posts: [
        { title: 'Five home activities that build early problem solving', category: 'Activities · 5 min read', excerpt: 'Everyday ideas that support focus, sequencing, patience, and playful thinking.', date: 'May 2, 2026', views: '689 views', media: 'media-three' }
      ]
    }
  };

  const params = new URLSearchParams(window.location.search);
  const requestedAuthor = params.get('author') || 'sana-kapoor';
  const author = authorProfiles[requestedAuthor] || authorProfiles['sana-kapoor'];
  const postCount = author.posts.length;
  const totalViews = author.posts.reduce((total, post) => {
    const raw = post.views.replace(' views', '');
    return total + (raw.includes('k') ? parseFloat(raw) * 1000 : parseInt(raw, 10));
  }, 0);
  const formattedViews = totalViews >= 1000 ? `${(totalViews / 1000).toFixed(1)}k` : `${totalViews}`;

  document.title = `${author.name} | Makoons Author`;
  authorDirectoryPage.querySelectorAll('[data-author-name]').forEach((node) => {
    node.textContent = author.name;
  });
  authorDirectoryPage.querySelector('[data-author-bio]').textContent = author.bio;
  authorDirectoryPage.querySelector('[data-author-image]').src = author.image;
  authorDirectoryPage.querySelector('[data-author-image]').alt = author.name;
  authorDirectoryPage.querySelector('[data-author-count]').textContent = postCount;
  authorDirectoryPage.querySelector('[data-author-views]').textContent = formattedViews;
  authorDirectoryPage.querySelector('[data-author-topic]').textContent = author.topic;
  authorDirectoryPage.querySelector('[data-author-intro]').textContent = `${author.name.split(' ')[0]} has written ${postCount} ${postCount === 1 ? 'article' : 'articles'} for preschool families.`;
  authorDirectoryPage.querySelector('[data-author-posts]').innerHTML = author.posts.map((post) => `
    <article class="author-post-card">
      <a class="card-media ${post.media}" href="article-details.html" aria-label="Read ${post.title}"></a>
      <div>
        <span>${post.category}</span>
        <h3><a href="article-details.html">${post.title}</a></h3>
        <p>${post.excerpt}</p>
        <small>${post.date} · ${post.views}</small>
      </div>
    </article>
  `).join('');
}


// Reusable listing pagination
const paginationConfigs = [
  { id: 'articles', list: '.all-posts-groups', item: '.all-post-card', pageSize: 12, group: '[data-article-group]' },
  { id: 'stories', list: '.stories-grid', item: '.story-library-card', pageSize: 9 },
  { id: 'printables', list: '.printable-library-grid', item: '.printable-library-card', pageSize: 12 },
  { id: 'sessions', list: '.sessions-card-grid', item: '.session-list-card', pageSize: 12 },
  { id: 'authors', list: '.author-post-list', item: '.author-post-card', pageSize: 12 }
];

const paginationInstances = new Map();

const createPagination = (config) => {
  const list = document.querySelector(config.list);
  if (!list) return null;

  const controls = document.createElement('nav');
  controls.className = 'pagination-nav';
  controls.setAttribute('aria-label', 'Pagination');
  controls.innerHTML = '<button type="button" data-page-prev>Previous</button><div class="pagination-pages" data-page-numbers></div><button type="button" data-page-next>Next</button>';
  list.insertAdjacentElement('afterend', controls);

  const previous = controls.querySelector('[data-page-prev]');
  const next = controls.querySelector('[data-page-next]');
  const numbers = controls.querySelector('[data-page-numbers]');
  let currentPage = 1;

  const getItems = () => [...list.querySelectorAll(config.item)];
  const getVisibleItems = () => getItems().filter((item) => !item.classList.contains('is-filter-hidden'));

  const syncGroups = () => {
    if (!config.group) return;
    document.querySelectorAll(config.group).forEach((group) => {
      const hasVisibleCard = [...group.querySelectorAll(config.item)].some((item) => !item.classList.contains('is-filter-hidden') && !item.classList.contains('is-page-hidden'));
      group.classList.toggle('is-page-hidden', !hasVisibleCard);
    });
  };

  const render = (reset = false) => {
    const visibleItems = getVisibleItems();
    const totalPages = Math.max(1, Math.ceil(visibleItems.length / config.pageSize));
    if (reset) currentPage = 1;
    currentPage = Math.min(currentPage, totalPages);

    getItems().forEach((item) => item.classList.add('is-page-hidden'));
    visibleItems.forEach((item, index) => {
      const page = Math.floor(index / config.pageSize) + 1;
      item.classList.toggle('is-page-hidden', page !== currentPage);
    });

    numbers.innerHTML = Array.from({ length: totalPages }, (_, index) => {
      const page = index + 1;
      return `<button type="button" class="${page === currentPage ? 'active' : ''}" data-page-number="${page}" aria-label="Go to page ${page}" aria-current="${page === currentPage ? 'page' : 'false'}">${page}</button>`;
    }).join('');

    previous.disabled = currentPage === 1;
    next.disabled = currentPage === totalPages;
    controls.classList.toggle('is-hidden', visibleItems.length <= config.pageSize);
    syncGroups();
  };

  previous.addEventListener('click', () => {
    currentPage = Math.max(1, currentPage - 1);
    render();
    list.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  next.addEventListener('click', () => {
    currentPage += 1;
    render();
    list.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  numbers.addEventListener('click', (event) => {
    const button = event.target.closest('[data-page-number]');
    if (!button) return;
    currentPage = Number(button.dataset.pageNumber || 1);
    render();
    list.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  render(true);
  return { id: config.id, refresh: (reset = false) => render(reset) };
};

paginationConfigs.forEach((config) => {
  const instance = createPagination(config);
  if (instance) paginationInstances.set(config.id, instance);
});

// Story Search + Category Filtering
const storySearch = document.querySelector('[data-story-search]');
const storyCategoryButtons = document.querySelectorAll('[data-story-category-filter]');
const storyCards = document.querySelectorAll('.story-library-card[data-story-category]');

if (storyCards.length > 0) {
  let activeStoryCategoryFilter = 'all';

  const updateStories = () => {
    const query = storySearch ? storySearch.value.trim().toLowerCase() : '';

    storyCards.forEach((card) => {
      const category = card.dataset.storyCategory || '';
      const title = card.dataset.title?.toLowerCase() || '';
      const cardText = card.textContent.toLowerCase();
      const searchableText = `${title} ${cardText} ${category}`;

      const matchesCategory = activeStoryCategoryFilter === 'all' || category === activeStoryCategoryFilter;
      const matchesSearch = !query || searchableText.includes(query);
      const isVisible = matchesCategory && matchesSearch;

      card.classList.toggle('is-filter-hidden', !isVisible);
    });

    if (window.makoonsPagination?.refresh) {
      window.makoonsPagination.refresh('stories', true);
    }
  };

  if (storySearch) {
    storySearch.addEventListener('input', updateStories);
  }

  storyCategoryButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      storyCategoryButtons.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      activeStoryCategoryFilter = btn.dataset.storyCategoryFilter || 'all';
      updateStories();
    });
  });
}

// Printable Search Filtering
const printableSearch = document.querySelector('[data-printable-search]');

if (printableSearch) {
  console.log('Printable search input found, attaching event listener.');
  printableSearch.addEventListener('input', () => {
    const query = printableSearch.value.trim().toLowerCase();
    console.log('Searching printables for query:', query);
    const printableCards = document.querySelectorAll('.printable-library-card');
    console.log('Found printable cards count:', printableCards.length);

    let visibleCount = 0;
    printableCards.forEach((card) => {
      const title = card.dataset.title?.toLowerCase() || '';
      const cardText = card.textContent.toLowerCase();
      const searchableText = `${title} ${cardText}`;

      const matchesSearch = !query || searchableText.includes(query);
      card.classList.toggle('is-filter-hidden', !matchesSearch);
      if (matchesSearch) {
        visibleCount += 1;
      }
    });

    const emptyState = document.querySelector('[data-empty-state]');
    if (emptyState) {
      emptyState.classList.toggle('show', visibleCount === 0);
    }

    if (window.makoonsPagination?.refresh) {
      window.makoonsPagination.refresh('printables', true);
    }
  });
}

// Sessions Search + Category Filtering
const sessionSearch = document.querySelector('[data-session-search]');
const sessionCategoryButtons = document.querySelectorAll('[data-session-category-filter]');
const sessionCards = document.querySelectorAll('.session-list-card');

if (sessionCards.length > 0) {
  let activeSessionCategoryFilter = 'all';

  const updateSessions = () => {
    const query = sessionSearch ? sessionSearch.value.trim().toLowerCase() : '';
    let visibleCount = 0;

    sessionCards.forEach((card) => {
      const category = card.dataset.sessionCategory || '';
      const title = card.dataset.title?.toLowerCase() || '';
      const cardText = card.textContent.toLowerCase();
      const searchableText = `${title} ${cardText} ${category}`;

      const matchesCategory = activeSessionCategoryFilter === 'all' || category === activeSessionCategoryFilter;
      const matchesSearch = !query || searchableText.includes(query);
      const isVisible = matchesCategory && matchesSearch;

      card.classList.toggle('is-filter-hidden', !isVisible);
      if (isVisible) {
        visibleCount += 1;
      }
    });

    const emptyState = document.querySelector('[data-empty-state]');
    if (emptyState) {
      emptyState.classList.toggle('show', visibleCount === 0);
    }

    if (window.makoonsPagination?.refresh) {
      window.makoonsPagination.refresh('sessions', true);
    }
  };

  if (sessionSearch) {
    sessionSearch.addEventListener('input', updateSessions);
  }

  sessionCategoryButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      sessionCategoryButtons.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      activeSessionCategoryFilter = btn.dataset.sessionCategoryFilter || 'all';
      updateSessions();
    });
  });
}

// In-Place Video Player Logic
function getYoutubeEmbedUrl(url) {
  if (!url) return '';
  let videoId = '';
  
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
  const match = url.match(regExp);
  
  if (match && match[2].length === 11) {
    videoId = match[2];
  }
  
  if (videoId) {
    return `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
  }
  return url;
}

document.addEventListener('click', (e) => {
  const watchLink = e.target.closest('.sessions-watch-link');
  if (watchLink) {
    e.preventDefault();
    const card = watchLink.closest('.sessions-feature-video');
    const thumb = card ? card.querySelector('.sessions-feature-thumb') : null;
    if (thumb) thumb.click();
    return;
  }

  const thumb = e.target.closest('.session-thumb, .sessions-feature-thumb');
  if (!thumb) return;

  const videoUrl = thumb.dataset.videoUrl || thumb.getAttribute('href');
  if (!videoUrl || videoUrl.includes('sessions')) return;

  if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
    e.preventDefault();
    const embedUrl = getYoutubeEmbedUrl(videoUrl);
    
    // Store original href and disable it to prevent link redirection on border clicks
    thumb.dataset.originalHref = thumb.getAttribute('href');
    thumb.setAttribute('href', 'javascript:void(0)');
    thumb.classList.add('video-playing');
    thumb.style.pointerEvents = 'auto';
    
    const iframe = document.createElement('iframe');
    iframe.src = embedUrl;
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = '0';
    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
    iframe.setAttribute('allowfullscreen', 'true');
    iframe.style.pointerEvents = 'auto';
    
    // Clear thumbnail contents (play icon, badges) and insert iframe
    thumb.innerHTML = '';
    thumb.appendChild(iframe);
  }
});

window.makoonsPagination = {
  refresh(id, reset = false) {
    const instance = paginationInstances.get(id);
    if (instance) instance.refresh(reset);
  }
};

// WordPress admin-ajax Subscription Form Handler
document.addEventListener("DOMContentLoaded", function () {
  const subscribeForms = document.querySelectorAll("form.newsletter-form, form.trending-subscribe");

  subscribeForms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const submitBtn = form.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.textContent : 'Subscribe';

      // Find or create a message container to show success/error
      let msgContainer = form.querySelector('.subscription-msg');
      if (!msgContainer) {
        msgContainer = document.createElement('div');
        msgContainer.className = 'subscription-msg font-12 mt-2';
        form.appendChild(msgContainer);
      }

      const formData = new FormData(form);
      formData.append("action", "add_subscriber");

      // Disable button and clear previous message
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
      }
      msgContainer.innerHTML = '';

      fetch("https://makoons.com/blogs/wp-admin/admin-ajax.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.text())
        .then((responseText) => {
          console.log("Response:", responseText);
          if (responseText.includes('success') || responseText.includes('Already') || responseText.includes('successfully')) {
            msgContainer.innerHTML = "<span style='color: #2e7d32; font-weight: 500;'>🎉 Subscribed successfully!</span>";
          } else {
            msgContainer.innerHTML = "<span style='color: #2e7d32; font-weight: 500;'>" + responseText + "</span>";
          }
          form.reset();
        })
        .catch((error) => {
          console.error("Error:", error);
          msgContainer.innerHTML = "<span style='color: #c62828; font-weight: 500;'>❌ Error connecting to subscription service.</span>";
        })
        .finally(() => {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
          }
        });
    });
  });

  // Dynamic Table of Contents (TOC) for Blog and Story details
  const articleContent = document.querySelector('[data-article-content]');
  const tocContainer = document.getElementById('toc-container');
  const tocList = document.getElementById('toc-list');

  if (articleContent && tocContainer && tocList) {
    const headings = articleContent.querySelectorAll('h2, h3');
    const TOC_VISIBLE_LIMIT = 6;

    if (headings.length > 0) {
      headings.forEach((heading, index) => {
        // Generate dynamic ID if it doesn't exist
        if (!heading.id) {
          const slug = heading.textContent
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
          heading.id = slug || `section-${index + 1}`;
        }

        // Add to Table of Contents list
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = `#${heading.id}`;
        a.textContent = heading.textContent;
        li.appendChild(a);

        // Indent sub-headings (h3)
        if (heading.tagName.toLowerCase() === 'h3') {
          li.classList.add('toc-subheading');
        }

        // Hide items beyond limit initially
        if (index >= TOC_VISIBLE_LIMIT) {
          li.classList.add('toc-hidden-item');
          li.style.display = 'none';
        }

        tocList.appendChild(li);

        // Smooth scroll implementation
        a.addEventListener('click', (e) => {
          e.preventDefault();
          const target = document.getElementById(heading.id);
          if (target) {
            const offset = 90; // offset for sticky header
            const bodyRect = document.body.getBoundingClientRect().top;
            const elementRect = target.getBoundingClientRect().top;
            const elementPosition = elementRect - bodyRect;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
              top: offsetPosition,
              behavior: 'smooth'
            });

            // Update URL hash without jumping
            history.pushState(null, null, `#${heading.id}`);
          }
        });
      });

      // Add Read more / Show less button if more than limit
      if (headings.length > TOC_VISIBLE_LIMIT) {
        const toggleLi = document.createElement('li');
        toggleLi.classList.add('toc-toggle-item');
        const remaining = headings.length - TOC_VISIBLE_LIMIT;

        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'toc-toggle-btn';
        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg> Read more <span class="toc-toggle-count">(${remaining} more)</span>`;

        toggleBtn.addEventListener('click', () => {
          const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
          const hiddenItems = tocList.querySelectorAll('.toc-hidden-item');

          if (!isExpanded) {
            hiddenItems.forEach(item => { item.style.display = ''; });
            toggleBtn.setAttribute('aria-expanded', 'true');
            toggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg> Show less`;
          } else {
            hiddenItems.forEach(item => { item.style.display = 'none'; });
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg> Read more <span class="toc-toggle-count">(${remaining} more)</span>`;
          }
        });

        toggleLi.appendChild(toggleBtn);
        tocList.appendChild(toggleLi);
      }

      // Show the TOC container
      tocContainer.style.display = 'block';
    }
  }
});
