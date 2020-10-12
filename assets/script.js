( function($) {
	// Wait for DOM to be ready.
	document.addEventListener( 'DOMContentLoaded', function() {
        
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

        function validateEmail( mail ) {
            if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)) {
                return (true)
            }

            return (false)
        }

        
    })
} )(jQuery)
