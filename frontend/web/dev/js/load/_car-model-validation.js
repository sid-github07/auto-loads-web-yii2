/* global
formId,
widgetContainer,
LoadCar,
LOAD_CAR_MODEL_IS_REQUIRED,
CAR_MODEL_MAX_NUMBER_OF_DIGITS,
TOO_MANY_CAR_MODEL_NUMBER_OF_DIGITS
*/

/**
 * Registers load car model validation
 * @todo naudojamas tiek announce tiek index
 */
function registerCarModelValidation() {
    $('#' + formId).ready(function () {
        toggleCarModelValidation();
        $('.' + widgetContainer).on('afterInsert', function () {
            toggleCarModelValidation();
        }).on('afterDelete', function () {
            toggleCarModelValidation();
        });
    });
}

/**
 * Adds and removes load car model validation
 * @todo naudojamas tiek announce tiek index
 */
function toggleCarModelValidation() {
    /** @member {number} Number of different load car models */
    var modelsCount = $('.model').length;
    for (var index = 0; index < modelsCount; index++) {
        removeCarModelValidation(index);
        addCarModelValidation(index);
    }
}

/**
 * Removes validation from load car model
 * @todo naudojamas tiek announce tiek index
 *
 * @param {number} index Load car model index
 */
function removeCarModelValidation(index) {
    $('#' + formId).yiiActiveForm('remove', LoadCar + '-' + index + '-model');
}

/**
 * Adds validation to load car model
 *
 * @param {number} index Load car model index
 * @todo naudojamas tiek announce tiek index
 */
function addCarModelValidation(index) {
    $('#' + formId).yiiActiveForm('add', {
        id: LoadCar + '-' + index + '-model',
        name: '[' + index + ']model',
        container: '.field-' + LoadCar + '-' + index + '-model',
        input: '#' + LoadCar + '-' + index + '-model',
        error: '.help-block.help-block-error',
        validate: function validate(attribute, value, messages) {
            validateCarModelNumberOfDigits(value, messages);
            if (isLoadTypePartial()) {
                yii.validation.required(value, messages, {"message": LOAD_CAR_MODEL_IS_REQUIRED});
            }
        }
    });
}

/**
 * Validates whether car model number of digits do not exceed the maximum value
 *
 * @param {string} carModel Load car model value
 * @param {object} messages List of error messages
 * @todo naudojamas tiek announce tiek index
 */
function validateCarModelNumberOfDigits(carModel, messages) {
    if (typeof carModel === "undefined") {
        return;
    }

    if (countDigits(carModel) > CAR_MODEL_MAX_NUMBER_OF_DIGITS) {
        yii.validation.addMessage(messages, TOO_MANY_CAR_MODEL_NUMBER_OF_DIGITS, carModel);
    }
}

/**
 * Counts and returns number of digits from provided string
 *
 * @param {string} value Target string wherein counted digits
 * @todo naudojamas tiek announce tiek index
 */
function countDigits(value) {
    return value.replace(/[^0-9]/g,"").length;
}