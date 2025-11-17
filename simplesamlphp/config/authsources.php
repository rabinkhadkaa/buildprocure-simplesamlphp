<?php

$config = [


    // This is a authentication source which handles admin authentication.
    'admin' => [
        // The default is to use core:AdminPassword, but it can be replaced with
        // any authentication source.

        'core:AdminPassword',
    ],


    // An authentication source which can authenticate against SAML 2.0 IdPs.
    'default-sp' => [
        'saml:SP',

        // The entity ID of this SP.
        'entityID' => 'https://myapp.example.org/',

        // The entity ID of the IdP this SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => null,

        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => null,

        /*
         * If SP behind the SimpleSAMLphp in IdP/SP proxy mode requests
         * AuthnContextClassRef, decide whether the AuthnContextClassRef will be
         * processed by the IdP/SP proxy or if it will be passed to the original
         * IdP in front of the IdP/SP proxy.
         */
        'proxymode.passAuthnContextClassRef' => false,

        /*
         * The attributes parameter must contain an array of desired attributes by the SP.
         * The attributes can be expressed as an array of names or as an associative array
         * in the form of 'friendlyName' => 'name'. This feature requires 'name' to be set.
         * The metadata will then be created as follows:
         * <md:RequestedAttribute FriendlyName="friendlyName" Name="name" />
         */
        /*
        'name' => [
            'en' => 'A service',
            'no' => 'En tjeneste',
        ],

        'attributes' => [
            'attrname' => 'urn:oid:x.x.x.x',
        ],
        'attributes.required' => [
            'urn:oid:x.x.x.x',
        ],
        */
    ],

    'auth0' => [
        'saml:SP',

        // The entity ID of this SP.
        'entityID' => 'http://buildprocure.com/simplesamlphp/simplesaml/module.php/saml/sp/metadata.php/auth0',

        // The entity ID of the IdP this SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => 'urn:dev-6xu0s3t43xer76kc.us.auth0.com',

        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => null,
        'privatekey' => '/var/www/app/simplesamlphp/cert/saml.pem',      // relative to SimpleSAMLphp root
        'certificate' => '/var/www/app/simplesamlphp/cert/saml.crt',
        'redirect.sign' => true,
        'sign.logout' => true,
        'sign.authnrequest' => true,

        // NameIDPolicy MUST use proper casing for v2.x
        'NameIDPolicy' => [
            'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
            'AllowCreate' => true,
            'SPNameQualifier' => 'http://buildprocure.com/simplesamlphp/simplesaml/module.php/saml/sp/metadata.php/auth0',
        ],

        // Optional: explicitly set ACS URL (SimpleSAMLphp usually sets this automatically)
        'AssertionConsumerServiceURL' => 'http://buildprocure.com/simplesamlphp/simplesaml/module.php/saml/sp/saml2-acs.php/auth0',

    ],


];
