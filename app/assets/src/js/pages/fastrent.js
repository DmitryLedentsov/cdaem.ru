$(document).on('submit', '#form-reservation', function (e) {
    e.preventDefault();
    let $form = $(this);
    console.log($form);

    window.ajaxRequest($form, {
        success: function (response) {

        }
    });
});



/*
const next = document.querySelector('.btn-fast-next');
const back = document.querySelector('.btn-fast-back');
const submit = document.querySelector('.btn-fast-submit');
const steps = document.querySelectorAll('.steps');
const activeClass = 'active';
const hiddenClass = 'd-none';
let currentStep = 1;

const buttonsAvaliable = () => {
    currentStep === 1 ? back.classList.add(hiddenClass) : back.classList.remove(hiddenClass);

    if (currentStep === steps.length) {
        next.classList.add(hiddenClass);
        submit.classList.remove(hiddenClass)
    } else {
        next.classList.remove(hiddenClass);
        submit.classList.add(hiddenClass)
    }
}

const changeStep = () => {
    document.querySelector('.steps.' + activeClass).classList.remove(activeClass);
    steps[currentStep - 1].classList.add(activeClass);
    buttonsAvaliable()
}

next.addEventListener('click', () => {
    ++currentStep;
    changeStep();
});

back.addEventListener('click', () => {
    --currentStep;
    changeStep();
})
*/
