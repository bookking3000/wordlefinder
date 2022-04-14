/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

let activeFilterIndex = undefined;
let arrayOfInputWithFilteredChars = [];

function setupForbiddenCharInputHandler() {
    let formIndexOfForbiddenChars = $('#word_finder_form_indexOfForbiddenChars');

    $(".char-inp").on('click', (e) => {
        let target_id = e.target.id;
        activeFilterIndex = target_id.charAt(target_id.length - 1);
        $("#char_position").text(activeFilterIndex);

        if (arrayOfInputWithFilteredChars[activeFilterIndex] !== undefined) {
            formIndexOfForbiddenChars.val(arrayOfInputWithFilteredChars[activeFilterIndex]);
        } else {
            formIndexOfForbiddenChars.val("")
        }
    });

    formIndexOfForbiddenChars.on('input', (e) => {
        if (activeFilterIndex === undefined) {
            alert("Bitte setze zunächst den Fokus auf einen Char-Input!")
            e.target.value = "";
        }

        arrayOfInputWithFilteredChars[activeFilterIndex] = e.target.value;
    });
}

function setUpResetButtonHandler() {
    $("#word_finder_form_reset").click(function () {
        arrayOfInputWithFilteredChars = [];
        activeFilterIndex = undefined;
        let formElements = $(':input', "[name='word_finder_form']");
        formElements.not(':button, :submit, :reset, :hidden')
            .val('')
            .attr('value', '')
            .prop('checked', false)
            .prop('selected', false);
    });
}

function setupFormSubmitHandler() {
    $("#word_finder_form_save").on('click', async (e) => {
        e.preventDefault();

        let form = $("[name='word_finder_form']");
        const formData = new FormData(form.get(0)); // We need the Vanilla DOM Element here.
        formData.append('forbiddenChars', arrayOfInputWithFilteredChars)
        fetch("/solver/",
            {
                body: formData,
                method: "post",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    form.replaceWith($(data.form.content).find("[name='word_finder_form']"));
                    $("#word-results-container").replaceWith($(data.form.content).find("#word-results-container"));
                    setupAllFormEventHandlers()
                }
            });
    })
}

function setupAllFormEventHandlers() {
    setUpResetButtonHandler();
    setupForbiddenCharInputHandler();
    setupFormSubmitHandler();
}

docReady(function () {
    setupAllFormEventHandlers();
});

function docReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}
