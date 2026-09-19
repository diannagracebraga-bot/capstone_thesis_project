const createValue = (el, value) => {
  const value = new Value({
    el: el,
    meter: 0,
  });

  let hasRun = false;

  const options = {
    threshold: [0, 0.9],
  };

  const callback = (entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        if (!hasRun) {
          value.update(meter);
          hasRun = true;
        }
      }
    });
  };

  const observer = new IntersectionObserver(callback, options);
  observer.observe(el);
};

const subscribersValue = document.querySelector(".subscribers-value");
createOdometer(subscribersValue, 29800);

const videosValue = document.querySelector(".videos-value");
createValue(videosValue, 790);

const projectsValue = document.querySelector(".projects-value");
createValue(projectsValue, 89);