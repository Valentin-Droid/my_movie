import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
    }

    searchMovie(e){

        const url = e.currentTarget.dataset.url;
        const searchValue = e.currentTarget.value;

        $.ajax({
            url: url,
            method: 'POST',
            data: { searchValue: searchValue},

            success: (data) => {
                    $('#movies-list').html(data.html);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

}
