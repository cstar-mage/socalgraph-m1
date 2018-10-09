<?php

class Blackbox_Api_Model_Oauth_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * @SWG\Post(
     *   path="/oauth/login",
     *   summary="Retrieve oauth token and secret by customer credentials and client key",
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          @SWG\Property(
     *              property="login",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="password",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="consumer_key",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="website_id",
     *              type="integer"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="An object with oauth credentials",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="response",
     *                  description="Oauth credentials",
     *                  type="object",
     *                  @SWG\Schema(
     *                      @SWG\Property(
     *                          property="oauth_token",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="oauth_token_secret",
     *                          type="string"
     *                      )
     *                  )
     *              )
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error",
     *     @SWG\Schema(ref="#/definitions/error_response")
     *   )
     * )
     *
     * @param $login
     * @param $password
     * @param $consumerKey
     * @param int $websiteId
     * @return mixed
     */
    public function login($login, $password, $consumerKey, $websiteId = 1)
    {
        /** @var Blackbox_Api_Model_Oauth_Server $server */
        $server = Mage::getModel('blackbox_api/oauth_server');

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($websiteId);
        $customer->authenticate($login, $password);

        $token = $server->generateAccessToken($consumerKey);

        $token->authorize($customer->getId(), Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER);

        $response['oauth_token'] = $token->getToken();
        $response['oauth_token_secret'] = $token->getSecret();

        return $response;
    }
}