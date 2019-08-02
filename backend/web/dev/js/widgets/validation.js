var validationUrls = [];

/**
 * Creates data which will allow to load POST to model in controller
 *
 * @param {string} name Current input field name
 * @param {string} value Current input field value
 * @returns {{}}
 */
function makeValidationData(name, value) {
    /** @member {object} POST data */
    var data = {};
    data[name] = value;
    return data;
}

/**
 * Returns model name in lowercase and dashes instead of spaces from input name
 *
 * @param {string} name Current input field name
 * @returns {string}
 */
function getModelByName(name) {
    /** @member {string} Model name, got from input name */
    var model = name.substring(0, name.indexOf('['));
    /** @member {object} Model name split in peaces */
    var splitModel = model.split(/(?=[A-Z])/);
    /** @member {string} Final model name */
    var modelName = '';
    $.each(splitModel, function (key, value) {
        modelName += value.toLowerCase() + '-';
    });
    return modelName.substring(0, modelName.length - 1);
}

/**
 * Returns input attribute name from input name
 *
 * @param {string} name Current input field name
 * @returns {string}
 */
function getAttributeByName(name) {
    return name.substring(name.indexOf('[') + 1, name.indexOf(']')).toLowerCase();
}

/**
 * Adds validation to current input
 *
 * @param {string} formId Form ID of current input field
 * @param {string} inputId Current input ID
 * @param {string} model Current input model name in lowercase and dashes instead of spaces
 * @param {string} attribute Current input attribute name
 */
function addValidation(formId, inputId, model, attribute) {
    $('#' + formId).yiiActiveForm('add', {
        'id': model + '-' + attribute,
        'name': attribute,
        'container': '.field-' + inputId,
        'input': '#' + inputId,
        'error': '.help-block.help-block-error'
    });
}

/**
 * Validates current input
 *
 * @param {string} formId Form ID of current input field
 * @param {string} inputId Current input ID
 * @param {object} postData Data object that needs to be passed through the POST method
 * @param {string} model Current input model name in lowercase and dashes instead of spaces
 * @param {string} attribute Current input attribute name
 */
function validate(formId, inputId, postData, model, attribute) {
    $.post(validationUrls[inputId], postData, function (data) {
        /** @member {object} POST response object */
        var obj = $.parseJSON(data);
        /** @member {string} Validation ID */
        var id = model + '-' + attribute;
        $('#' + formId).yiiActiveForm('updateAttribute', id, obj[id]);
    });
}