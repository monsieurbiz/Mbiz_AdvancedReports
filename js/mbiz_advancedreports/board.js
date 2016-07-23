/**
 * This file is part of Mbiz_AdvancedReports for Magento.
 *
 * @license MIT
 * @author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com> <@jacquesbh>
 * @category Mbiz
 * @package Mbiz_AdvancedReports
 * @copyright Copyright (c) 2015 Monsieur Biz (http://monsieurbiz.com)
 */

var Board = {};
Board.params = {};
Board.form = null;

// Init the board
Board.init = function (params)
{
    // Keep the params
    this.params = params;

    // On change on the request selector
    var request_selector_id = this.params.request_selector_id;
    $(request_selector_id).observe('change', function (event) {
        var requestId = event.target.value;

        if (requestId.length) {
            // Load the request form
            new Ajax.Request(
                this.params.request_form_url, {
                    onSuccess: function (response) {
                        $(this.params.request_zone).update(response.responseText);
                        this.form = new varienForm(this.params.request_form_id, this.params.request_form_validate_url);
                    }.bind(this),
                    parameters: {
                        request_id: requestId
                    }
                }
            );
        } else {
            $(this.params.request_zone).update('');
        }
    }.bind(this));
};

// Submit the request form to get an export CSV file
Board.export = function ()
{
    $(this.params.export_flag_field_id).value = '1';
    this.form.submit();
    return false;
};

// Submit the request form to display the results
Board.submit = function ()
{
    $(this.params.export_flag_field_id).value = '0';
    this.form.submit();
    return false;
};
