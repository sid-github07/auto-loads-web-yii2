/* global
formId,
LoadCar,
QUANTITY_MAX_VALUE,
TOTAL_QUANTITY_TOO_BIG,
QUANTITY_IS_REQUIRED,
QUANTITY_NOT_INTEGER,
QUANTITY_MIN_VALUE,
QUANTITY_TOO_SMALL,
QUANTITY_TOO_BIG
*/

/**
 * Registers car quantity validation
 * @todo naudojamas tiek announce tiek index
 */
function registerQuantityValidation() {
    $('.quantity').on('change blur', function () {
        validateQuantity();
    });
}

/**
 * Validates load car quantity elements
 * @todo naudojamas tiek announce tiek index
 */
function validateQuantity() {
    for (var index = 0; index < $('.quantity').length; index++) {
        $('#' + formId).yiiActiveForm('validateAttribute', LoadCar + '-' + index + '-quantity');
    }
}

/**
 * Removes and adds validation to car quantity elements
 * @todo naudojamas tiek announce tiek index
 */
function toggleQuantityValidation() {
    for (var index = 0; index < $('.quantity').length; index++) {
        removeQuantityValidation(index);
        addQuantityValidation(index);
    }
}

/**
 * Removes validation from car quantity element
 *
 * @param {number} index Quantity element index
 * @todo naudojamas tiek announce tiek index
 */
function removeQuantityValidation(index) {
    $('#' + formId).yiiActiveForm('remove', LoadCar + '-' + index + '-quantity');
}

/**
 * Adds validation to car quantity element
 *
 * @param {number} index Quantity element index
 * @todo naudojamas tiek announce tiek index
 */
function addQuantityValidation(index) {
    $('#' + formId).yiiActiveForm('add', {
        id: LoadCar + '-' + index + '-quantity',
        name: '[' + index + ']quantity',
        container: '.field-' + LoadCar + '-' + index + '-quantity',
        input: '#' + LoadCar + '-' + index + '-quantity',
        error: '.help-block.help-block-error',
        validate: function validate(attribute, value, messages) {
            // TODO: galima iškelti į funkciją
            if (parseInt(getTotalQuantity()) > parseInt(QUANTITY_MAX_VALUE)) {
                yii.validation.addMessage(messages, TOTAL_QUANTITY_TOO_BIG, value);
            }
            // TODO: galima iškelti į funkciją
            if (isLoadTypePartial()) {
                yii.validation.required(value, messages, {"message": QUANTITY_IS_REQUIRED});
            }
            // TODO: galima iškelti į funkciją
            yii.validation.number(value, messages, {
                "pattern": /^\s*[+-]?\d+\s*$/,
                "message": QUANTITY_NOT_INTEGER,
                "min": parseInt(QUANTITY_MIN_VALUE),
                "tooSmall": QUANTITY_TOO_SMALL,
                "max": parseInt(QUANTITY_MAX_VALUE),
                "tooBig": QUANTITY_TOO_BIG,
                "skipOnEmpty": 1
            });
        }
    });
}

/**
 * Returns total quantity of cars models
 *
 * @returns {number}
 * @todo naudojamas tiek announce tiek index
 */
function getTotalQuantity() {
    /** @member {number} Total quantity */
    var total = 0;
    $('#' + formId + ' .quantity').each(function () {
        total += Number($(this).val());
    });
    return total;
}