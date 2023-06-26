const countMedias = document.getElementsByClassName("mediasTotal");
let arrCount = [];

for(var l in countMedias)
{
  arrCount.push(countMedias[l]);
}

const displayMedias = arrCount.slice(0, 4);
var displayMediasMove = displayMedias;

(function() {
  "use strict";

  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  const scrollto = (el) => {
    let header = select('#header')
    let offset = header.offsetHeight

    if (!header.classList.contains('header-scrolled')) {
      offset -= 20
    }

    let elementPos = select(el).offsetTop
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    })
  }

  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
  })

  on('click', '.navbar .dropdown > a', function(e) {
    if (select('#navbar').classList.contains('navbar-mobile')) {
      e.preventDefault()
      this.nextElementSibling.classList.toggle('dropdown-active')
    }
  }, true)

  on('click', '.scrollto', function(e) {
    if (select(this.hash)) {
      e.preventDefault()

      let navbar = select('#navbar')
      if (navbar.classList.contains('navbar-mobile')) {
        navbar.classList.remove('navbar-mobile')
        let navbarToggle = select('.mobile-nav-toggle')
        navbarToggle.classList.toggle('bi-list')
        navbarToggle.classList.toggle('bi-x')
      }
      scrollto(this.hash)
    }
  }, true)

  window.addEventListener('load', () => {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash)
      }
    }

    // Afficher seulement les 4 premiers medias
    if(countMedias.length != 0)
    {
      for(var k in displayMediasMove)
      {
        displayMediasMove[k].classList.remove("mediaCarroussel");
        displayMediasMove[k].classList.add("mediaDisplay");
      }
    }
  });

  // Afficher seulement les premieres tricks

  let cont = document.getElementsByClassName("content");
  let arrToDisplay = [];
  
  for(var j in cont)
  {
    arrToDisplay.push(cont[j]);
  }
      
  arrToDisplay = arrToDisplay.slice(0, 22);
  
  for(var k in arrToDisplay)
  {
    arrToDisplay[k].classList.remove("content");
  }


  window.addEventListener('load', () => {

  //   let portfolioRows = document.getElementsByClassName('.portfolio-container');

  //   if (portfolioRows != null && portfolioRows.length > 0)
  //   {
  //     for(var i in portfolioRows)
  //     {
  //       let portfolioContainer = portfolioRows[i];
  //       let portfolioIsotope = new Isotope(portfolioContainer, {
  //         itemSelector: '.portfolio-item',
  //         layoutMode: 'fitRows'
  //       });
  //     }
  //   }
  // });
  
      let portfolioFilters = select('#portfolio-flters li', true);

      on('click', '#portfolio-flters li', function(e) {
        e.preventDefault();
        portfolioFilters.forEach(function(el) {
          el.classList.remove('filter-active');
        });
        this.classList.add('filter-active');

        portfolioIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        portfolioIsotope.on('arrangeComplete', function() {
          AOS.refresh()
        });
      }, true);
    });

  const portfolioLightbox = GLightbox({
    selector: '.portfolio-lightbox'
  });

  new Swiper('.portfolio-details-slider', {
    speed: 400,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    })
  });

  new PureCounter();

})()

function display()
{
  let count = document.getElementsByClassName("content");
  let arrCount = [];
  let displayTricks = [];

  for(var l in count)
  {
    arrCount.push(count[l]);
  }
  
  if(count.length == 0)
  {
    document.getElementById("loadMore").classList.add("noContent");
  }
  else
  {
    displayTricks = arrCount.slice(0, 22);

    for(var k in displayTricks)
    {
      displayTricks[k].classList.remove("content");
    }
  }
}

function displayMediasLeft()
{
  // let count = document.getElementsByClassName("mediaCarroussel");
  displayMediasMove = document.getElementsByClassName("mediaDisplay");

  let arrCount = [];
  let displayMediasL = [];

  for(let l in countMedias)
  {
    arrCount.push(countMedias[l]);
  }

  for(let x in displayMediasMove)
  {
    displayMediasL.push(displayMediasMove[x]);
  }

  let idElemToDisplay = arrCount.indexOf(displayMediasL[0]);
  console.log(idElemToDisplay);

  if(!idElemToDisplay <= 0)
  {
    let elemToDisplay = arrCount[idElemToDisplay - 1];
    elemToDisplay.classList.add("mediaDisplay");
    elemToDisplay.classList.remove("mediaCarroussel");

    let elemToHide = displayMediasL[3];
    elemToHide.classList.add("mediaCarroussel");
    elemToHide.classList.remove("mediaDisplay");
  }
  else
  {
    console.log("COUCOU");
    console.log(idElemToDisplay);
  }
}

function displayMediasRight()
{
  displayMediasMove = document.getElementsByClassName("mediaDisplay");

  let arrCount = [];
  let displayMediasR = [];

  for(let l in countMedias)
  {
    arrCount.push(countMedias[l]);
  }
  for(let x in displayMediasMove)
  {
    displayMediasR.push(displayMediasMove[x]);
  }

  // let idElemToDisplay = arrCount.indexOf(displayMediasR[displayMediasR.length -1]);
  let idElemToDisplay = arrCount.indexOf(displayMediasR[3]);
  console.log(idElemToDisplay);

  if(idElemToDisplay != arrCount.length -1)
  {
    let elemToDisplay = arrCount[idElemToDisplay + 1];
    elemToDisplay.classList.add("mediaDisplay");
    elemToDisplay.classList.remove("mediaCarroussel");

    let elemToHide = displayMediasR[0];
    elemToHide.classList.add("mediaCarroussel");
    elemToHide.classList.remove("mediaDisplay");
  }
  else
  {
    console.log("COUCOU");
    console.log(idElemToDisplay);
  }
}

function displayMoreDetailsBottom()
{
  let count = document.getElementsByClassName("commentsToShow");
  let arrCount = [];

  for(var l in count)
  {
    arrCount.push(count[l]);
  }
  
  if(count.length == 0)
  {
    document.getElementById("loadMoreDetailsBottom").classList.add("noContent");
  }
  else
  {
    arrToDisplay = arrCount.slice(0, 22);

    for(var k in arrToDisplay)
    {
      arrToDisplay[k].classList.remove("content");
    }
  }
}

function displayMiniMedias()
{
  let medias= document.getElementsByClassName("miniHideMedias");
  let hiddenMedias = document.getElementsByClassName("mediaCarroussel");

  if(medias.length == 0)
  {
    document.getElementById("loadMoreMedias").classList.add("noContent");
  }
  else
  {
      medias[0].classList.remove("miniHideMedias");
      document.getElementsByClassName("arrowLeft")[0].classList.add("minidisappear");
      document.getElementsByClassName("arrowRight")[0].classList.add("minidisappear");

      for(var i in hiddenMedias)
      {
         var hiddenMedia = hiddenMedias[i];
         hiddenMedia.classList.add("miniAppear");
      }
  }
}

function deleteConfirm(id)
{
    var confirmation = window.confirm('Etes-vous sûr de vouloir supprimer cette figure ?') ;
    if(confirmation == true)
    {
       //window.location.href = "figure/delete/" + $id;
       const queryString = window.location.search;
       console.log(queryString);
       const urlParams = new URLSearchParams(queryString);
       location.replace("/figure/delete/"+ id);
    }
}

function toggleMediasEdition()
{
   var sectionToAppear = document.querySelector('.anchorEditVisibility');
   sectionToAppear.classList.remove("editMedias");

   var sectionToHide = document.querySelector('.addMedia');
   sectionToHide.classList.add("editMedias");
}

// $(function () {
//   $('[data-toggle="tooltip"]').tooltip()
// })