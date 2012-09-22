<?php
namespace WebStream;
class TestValidateErrorHandlingController extends CoreController {
    
    public function validate1() {}
    
    /**
     * @Inject
     * @Error("Validate")
     */
    public function validate_error($params) {
        echo $params["error"]["rule"];
    }
}