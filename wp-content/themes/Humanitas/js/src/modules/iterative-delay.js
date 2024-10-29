const iterativeDelay = () => {
  const delayElements = document.querySelectorAll('.js-delay');
  delayElements.forEach((delayElement) => {
    // get js-delay-item elements where the closest .fade-in parent doest not have .animate class

    const delayItems = delayElement.querySelectorAll('.js-delay-item');
    delayItems.forEach((dItem, index) => {
      const item = dItem;
      item.style.animationDelay = `${index * 0.05}s`;
    });
  });
};

export default iterativeDelay;
