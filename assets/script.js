( function($) {
	// Wait for DOM to be ready.
	document.addEventListener( 'DOMContentLoaded', function() {
        
        // The referral form
        $('.add-new-referral').on('click', function() {
            $('.referral-form').slideToggle();
        });

        // const referralForm = document.querySelector('.referral-form form');
        let referralForm = document.getElementById('referral-form');

        if( referralForm ) {
            referralForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                resetResponse();

                const url = wc_add_to_cart_params.ajax_url;
                let params = new URLSearchParams( new FormData( referralForm ) );
                let username = document.getElementById('username').value;
                let email = document.getElementById('email').value;

                if( username == '' || username.length <= 4 ) {
                    resetResponse( 'error', 'Username should be at least 5 characters');
                    return;
                }

                if( false === validateEmail( email ) ) {
                    resetResponse( 'error', 'Email is not validated');
                    return;
                }

                resetResponse( 'success', 'Submitting, please wait...' );

                fetch(url, {
                    method: "POST",
                    body: params
                }).then( res => res.json() )
                .catch( error => {
                })
                .then( response => {
                    if( response === 0 || response.status === 'error' ) {
                        resetResponse( 'error', response.message );
                        return;
                    } else if( response === 1  || response.status === 'success' ) {
                        resetResponse( 'success', response.message );

                        referralForm.reset();
                    }
                })

            });
        }


        // The affiliate link form
        $('.add-new-link').on('click', function() {
            $('.affiliate-form').slideToggle();
        });

        let affiliateForm = document.getElementById('affiliate-form');
        
        if( affiliateForm ) {
            affiliateForm.addEventListener('submit', function(e) {
                e.preventDefault();

                resetResponse();

                const url = wc_add_to_cart_params.ajax_url;
                let params = new URLSearchParams( new FormData( affiliateForm ) );
                let productLink = document.getElementById('product-link').value;
                let campaignName = document.getElementById('campaign-name').value;
                let affiliateLink = document.getElementById('affiliate-link');

                if( productLink == '' ) {
                    resetResponse( 'error', 'Please enter product link');
                    return;
                }

                // if( false === validateUrl( productLink ) ) {
                //     resetResponse( 'error', 'Product link is not valid');
                //     return;
                // }

                if( campaignName == '' || campaignName.length <= 1 ) {
                    resetResponse( 'error', 'Campaign name should be at least 2 characters');
                    return;
                }

                resetResponse( 'success', 'Creating link, please wait...' );

                fetch(url, {
                    method: "POST",
                    body: params
                }).then( res => res.json() )
                .catch( error => {
                })
                .then( response => {
                    if( response === 0 || response.status === 'error' ) {
                        resetResponse( 'error', response.message );
                        return;
                    } else if( response === 1  || response.status === 'success' ) {
                        resetResponse( 'success', 'Affiliate link created successfully, please copy it' );

                        affiliateLink.value = response.message
                        affiliateLink.focus();
                        affiliateLink.select()

                        console.log(response.message);
                    }
                })
            });
        }

        // Make the input field select all text on click
        const affiliateInput = document.querySelectorAll('.affiliate-links-table .affiliate-link input');

        affiliateInput.forEach(function(el) {
            el.addEventListener('focus', function() {
                this.select();
            });
        });

        // Reset the response message
        function resetResponse( status = null, message = null ) {
            const responseMsg = document.querySelector('.form-response');

            if( message === null ) {
                responseMsg.innerHTML = '';
            } else {
                responseMsg.innerHTML = message;
            }
            
            if ( status !== null ) {
                if( status === 'error' ) {
                    responseMsg.classList.add( 'error' );
                    responseMsg.classList.remove( 'success' );
                } else if ( status === 'success' ) {
                    responseMsg.classList.add( 'success' );
                    responseMsg.classList.remove( 'error' );
                }
            }
        }

        // Validate email
        function validateEmail( mail ) {
            if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)) {
                return (true)
            }

            return (false)
        }

        // Validate URL
        function validateUrl(value) {
            var expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi
            var regexp = new RegExp(expression);
            return regexp.test(value);
        } 
    })
} )(jQuery)
