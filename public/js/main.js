'use strict';

$.material.init()

$(document).ready(function() {

    var lastImageFile = null;

    // perform upload
    $('#imageUploadForm').on('submit', function($e) {
        $e.preventDefault();

        var $form = $(this);
        var formData = new FormData($(this)[0]);

        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $('#imageResult').empty();
                $('#classificationResult').empty();
                $('#histogramResult').empty();
                $('#edgeDetectionResult').empty();
                $('.btn-primary', $form).attr('disabled', 'disabled').text('Processing');
            }
        })
        .done(function(data) {

            // store last image file
            lastImageFile = data.data.image;

            // add image
            var $imgContainer = $('<div></div>');
            $imgContainer.addClass('image-container');
            var $img = $('<img />');
            $img.attr('src', data.data.image);
            $imgContainer.append($img);
            $('#imageResult').append($imgContainer);

            // add classification
            $('#classificationResult').html('Image classified as <strong>' + data.data.classification + '</strong>');
        })
        .fail(function(xhr, status, err) {
            console.error(err.toString());
        })
        .always(function() {
            $('.btn-primary', $form).removeAttr('disabled').text('Send');
        });

    });

    // reclassify image
    $('#classify').on('click', function($e) {
        $e.preventDefault();

        if (!lastImageFile) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'ajax/processimage.php',
            data: {'do': 'reclassify', 'image': btoa(lastImageFile)},
            contentType: 'json',
            cache: false,
            beforeSend: function() {
                $('#classificationResult').empty();
                $('#classify').attr('disabled', 'disabled').text('Processing');
            }
        })
        .done(function(data) {
            $('#classificationResult').html('Image classified as <strong>' + data.data.classification + '</strong>');
        })
        .fail(function(xhr, status, err) {
            console.error(err.toString());
        })
        .always(function() {
            $('#classify').removeAttr('disabled').text('Reclassify');
        });

    });

    // get the histogram of the image
    $('#histogram').on('click', function($e) {
        $e.preventDefault();

        if (!lastImageFile) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'ajax/processimage.php',
            data: {'do': 'histogram', 'image': btoa(lastImageFile)},
            contentType: 'json',
            cache: false,
            beforeSend: function() {
                $('#histogramResult').empty();
                $('#histogram').attr('disabled', 'disabled').text('Processing');
            }
        })
        .done(function(data) {
            $('#histogramResult').html(data.data.image);
        })
        .fail(function(xhr, status, err) {
            console.error(err.toString());
        })
        .always(function() {
            $('#histogram').removeAttr('disabled').text('Get Histogram');
        });

    });

    // get the edge detection of the image
    $('#edge').on('click', function($e) {
        $e.preventDefault();

        if (!lastImageFile) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'ajax/processimage.php',
            data: {'do': 'edge', 'image': btoa(lastImageFile)},
            contentType: 'json',
            cache: false,
            beforeSend: function() {
                $('#edgeResult').empty();
                $('#edge').attr('disabled', 'disabled').text('Processing');
            }
        })
        .done(function(data) {
            $('#edgeResult').html(data.data.image);
        })
        .fail(function(xhr, status, err) {
            console.error(err.toString());
        })
        .always(function() {
            $('#edge').removeAttr('disabled').text('Detect Edges');
        });

    });

});
