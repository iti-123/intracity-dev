/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {

    $('#searchForm').bootstrapValidator({

        message: false,
        //container: 'tooltip',
        trigger: null,
        live: 'enabled',

        feedbackIcons: {
            // valid: 'glyphicon glyphicon-ok',
            // invalid: 'glyphicon glyphicon-remove',
            // validating: 'glyphicon glyphicon-refresh'
        },
        fields: {

            city: {// field name
                validators: {
                    notEmpty: {
                        message: 'City field cannot be empty '
                    }

                }
            },
            departingDate: {// field name
                // encluded: false,
                validators: {
                    notEmpty: {
                        message: 'Date field cannot be empty'
                    }


                }
            },
            ServiceType: {// field name
                validators: {
                    notEmpty: {
                        message: 'Service field cannot be empty'
                    }

                }
            },
            fragile: {// field name
                validators: {
                    notEmpty: {
                        message: 'Fragile field cannot be empty'
                    }

                }
            },

            fromLocation: {// field name
                validators: {
                    notEmpty: {
                        message: 'From-Location field cannot be empty'
                    }

                }
            },
            weight: {// field name
                validators: {
                    notEmpty: {
                        message: 'Weight field cannot be empty'
                    }
                    //                        regexp: {
                    //                            regexp: /^[0-9]+$/,
                    //                            message: 'Only Number'
                    //                        }


                }
            },
            tolocation: {// field name
                validators: {
                    notEmpty: {
                        message: 'To-Location field cannot be empty'
                    }

                }
            },
            material: {// field name
                validators: {
                    notEmpty: {
                        message: 'Material field cannot be empty'
                    }

                }
            },
            NoParcel: {// field name
                validators: {
                    notEmpty: {
                        message: 'This field cannot be empty'
                    },
                    regexp: {
                        regexp: /^[1-9][0-9]*$/,
                        message: 'Only Number but not start with  Zero'
                    }


                }
            }


        }
    })
        .on('success.form.bv', function (e) {
            e.preventDefault();
        });
});