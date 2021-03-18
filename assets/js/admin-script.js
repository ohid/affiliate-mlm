( function($) {
	// Wait for DOM to be ready.
	document.addEventListener( 'DOMContentLoaded', function() {

        // Withdraw approval form
        //
        let withdrawApprovalForm = document.getElementById('withdraw-action-form');

        if( withdrawApprovalForm ) {
            withdrawApprovalForm.addEventListener('submit', function(e) {
                e.preventDefault();

                resetResponse();

                let params = new URLSearchParams( new FormData( withdrawApprovalForm ) );
                let withdrawAction = document.getElementsByName("withdraw-action");
                let check = 0;

                for(i=0;i<withdrawAction.length;i++){
                  if(withdrawAction[i].checked){
                    check++;
                    break;
                  }
                }

                // Validate if the withdraw action is picked
                if(! check){
                    resetResponse("error", "Please choose an action!");
                    return false;
                }

                // Submit the withdraw action request
                resetResponse("success", "Submitting request...");
                
                fetch(ajaxurl, {
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
                    }
                })
            });
        }

        // 
        // Settings Form
        //
        let settingsForm = document.getElementById('main_settings');

        if( settingsForm ) {
            settingsForm.addEventListener('submit', function(e) {
                e.preventDefault();

                resetResponse();

                let params = new URLSearchParams( new FormData( settingsForm ) );
                let amlm_sales_representative = document.getElementById("amlm_sales_representative").value;
                let amlm_unit_manager = document.getElementById("amlm_unit_manager").value;
                let amlm_manager = document.getElementById("amlm_manager").value;
                let amlm_senior_manager = document.getElementById("amlm_senior_manager").value;
                let amlm_executive_manager = document.getElementById("amlm_executive_manager").value;
                let amlm_ass_g_manager = document.getElementById("amlm_ass_g_manager").value;
                let amlm_general_manager = document.getElementById("amlm_general_manager").value;

                // Validate if the withdraw action is picked
                if( 
                    ! amlm_sales_representative.length ||
                    ! amlm_unit_manager.length ||
                    ! amlm_manager.length ||
                    ! amlm_senior_manager.length ||
                    ! amlm_executive_manager.length ||
                    ! amlm_ass_g_manager.length ||
                    ! amlm_general_manager.length
                ) {
                    resetResponse("error", "You can not leave a field empty");
                    return false;
                }

                // Submit the withdraw action request
                resetResponse("success", "Submitting request...");
                
                fetch(ajaxurl, {
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
                    }
                })
            });
        }


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

    })
} )(jQuery)
