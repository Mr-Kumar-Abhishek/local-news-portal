<?php

/**
 * Hindi (हिन्दी) Validation Language Lines
 * for the Hind Bihar News Website
 */
return [
    // General Validation
    'required'               => '{field} फ़ील्ड आवश्यक है।',
    'min_length'             => '{field} कम से कम {param} अक्षरों का होना चाहिए।',
    'max_length'             => '{field} {param} अक्षरों से अधिक नहीं हो सकता।',
    'exact_length'           => '{field} ठीक {param} अक्षरों का होना चाहिए।',
    'greater_than'           => '{field} में {param} से अधिक संख्या होनी चाहिए।',
    'greater_than_equal_to'  => '{field} में {param} से अधिक या बराबर संख्या होनी चाहिए।',
    'less_than'              => '{field} में {param} से कम संख्या होनी चाहिए।',
    'less_than_equal_to'     => '{field} में {param} से कम या बराबर संख्या होनी चाहिए।',
    'in_list'                => '{field} निम्नलिखित में से एक होना चाहिए: {param}।',
    'matches'                => '{field} फ़ील्ड {param} फ़ील्ड से मेल नहीं खाता।',
    'differs'                => '{field} फ़ील्ड {param} से भिन्न होना चाहिए।',
    'is_unique'              => '{field} पहले से उपयोग में है। कृपया कोई भिन्न {field} चुनें।',
    'is_not_unique'          => '{field} डेटाबेस में मौजूद होना चाहिए।',
    'is_natural'             => '{field} में केवल सकारात्मक संख्या होनी चाहिए।',
    'is_natural_no_zero'     => '{field} में शून्य से अधिक संख्या होनी चाहिए।',
    'alpha'                  => '{field} में केवल वर्णमाला के अक्षर हो सकते हैं।',
    'alpha_numeric'          => '{field} में केवल अक्षर और संख्याएँ हो सकती हैं।',
    'alpha_numeric_space'    => '{field} में केवल अक्षर, संख्याएँ और रिक्त स्थान हो सकते हैं।',
    'alpha_dash'             => '{field} में केवल अक्षर, संख्याएँ, अंडरस्कोर और डैश हो सकते हैं।',
    'numeric'                => '{field} में केवल संख्याएँ होनी चाहिए।',
    'integer'                => '{field} में पूर्णांक संख्या होनी चाहिए।',
    'decimal'                => '{field} में दशमलव संख्या होनी चाहिए।',
    'is_numeric'             => '{field} में केवल संख्यात्मक अक्षर होने चाहिए।',
    'valid_email'            => '{field} में मान्य ईमेल पता होना चाहिए।',
    'valid_emails'           => '{field} में सभी मान्य ईमेल पते होने चाहिए।',
    'valid_ip'               => '{field} में मान्य IP पता होना चाहिए।',
    'valid_base64'           => '{field} में मान्य base64 स्ट्रिंग होनी चाहिए।',
    'valid_url'              => '{field} में मान्य URL होना चाहिए।',
    'valid_url_strict'       => '{field} में मान्य URL होना चाहिए।',
    'valid_date'             => '{field} में मान्य दिनांक होना चाहिए।',
    'valid_json'             => '{field} में मान्य JSON होना चाहिए।',
    'regex_match'            => '{field} सही प्रारूप में नहीं है।',

    // File Upload Validation
    'uploaded'               => '{field} फ़ाइल अपलोड करने में विफल।',
    'max_size'               => '{field} फ़ाइल का आकार बहुत बड़ा है।',
    'is_image'               => '{field} एक मान्य छवि फ़ाइल नहीं है।',
    'mime_in'                => '{field} अनुमत फ़ाइल प्रकार का नहीं है।',
    'ext_in'                 => '{field} में अनुमत फ़ाइल एक्सटेंशन नहीं है।',
    'max_dims'               => '{field} छवि का आकार बहुत बड़ा है।',

    // Auth Validation
    'username'               => [
        'required'           => 'उपयोगकर्ता नाम आवश्यक है।',
        'min_length'         => 'उपयोगकर्ता नाम कम से कम 3 अक्षरों का होना चाहिए।',
        'max_length'         => 'उपयोगकर्ता नाम 30 अक्षरों से अधिक नहीं हो सकता।',
        'is_unique'          => 'यह उपयोगकर्ता नाम पहले से उपयोग में है।',
        'alpha_numeric'      => 'उपयोगकर्ता नाम में केवल अक्षर और संख्याएँ हो सकती हैं।',
    ],
    'email'                  => [
        'required'           => 'ईमेल पता आवश्यक है।',
        'valid_email'        => 'कृपया मान्य ईमेल पता दर्ज करें।',
        'is_unique'          => 'यह ईमेल पता पहले से पंजीकृत है।',
    ],
    'password'               => [
        'required'           => 'पासवर्ड आवश्यक है।',
        'min_length'         => 'पासवर्ड कम से कम 8 अक्षरों का होना चाहिए।',
        'max_length'         => 'पासवर्ड 72 अक्षरों से अधिक नहीं हो सकता।',
        'strong_password'    => 'पासवर्ड में कम से कम एक बड़ा अक्षर, एक छोटा अक्षर, एक संख्या और एक विशेष अक्षर होना चाहिए।',
    ],
    'pass_confirm'           => [
        'required'           => 'कृपया अपने पासवर्ड की पुष्टि करें।',
        'matches'            => 'पासवर्ड पुष्टिकरण मेल नहीं खाता।',
    ],

    // News / Article Validation
    'title'                  => [
        'required'           => 'शीर्षक आवश्यक है।',
        'min_length'         => 'शीर्षक कम से कम 5 अक्षरों का होना चाहिए।',
        'max_length'         => 'शीर्षक 255 अक्षरों से अधिक नहीं हो सकता।',
    ],
    'content'                => [
        'required'           => 'सामग्री आवश्यक है।',
        'min_length'         => 'सामग्री कम से कम 50 अक्षरों की होनी चाहिए।',
    ],
    'excerpt'                => [
        'max_length'         => 'अंश 500 अक्षरों से अधिक नहीं हो सकता।',
    ],
    'slug'                   => [
        'is_unique'          => 'यह URL स्लग पहले से उपयोग में है। कृपया शीर्षक बदलें।',
    ],
    'category_id'            => [
        'required'           => 'कृपया एक श्रेणी चुनें।',
        'integer'            => 'कृपया मान्य श्रेणी चुनें।',
    ],
    'status'                 => [
        'in_list'            => 'कृपया मान्य स्थिति चुनें।',
    ],
    'section'                => [
        'in_list'            => 'कृपया मान्य अनुभाग चुनें।',
    ],

    // Comment Validation
    'comment'                => [
        'required'           => 'कृपया अपनी टिप्पणी लिखें।',
        'min_length'         => 'टिप्पणी कम से कम 2 अक्षरों की होनी चाहिए।',
        'max_length'         => 'टिप्पणी 1000 अक्षरों से अधिक नहीं हो सकती।',
    ],
    'name'                   => [
        'required'           => 'नाम आवश्यक है।',
        'min_length'         => 'नाम कम से कम 2 अक्षरों का होना चाहिए।',
    ],

    // Media Validation
    'media_file'             => [
        'uploaded'           => 'कृपया अपलोड करने के लिए फ़ाइल चुनें।',
        'max_size'           => 'फ़ाइल का आकार 10MB से अधिक नहीं हो सकता।',
        'is_image'           => 'केवल छवि फ़ाइलें अपलोड की जा सकती हैं।',
        'mime_in'            => 'फ़ाइल प्रकार अनुमत नहीं है। केवल JPG, PNG, GIF और WebP की अनुमति है।',
    ],
    'alt_text'               => [
        'max_length'         => 'Alt टेक्स्ट 255 अक्षरों से अधिक नहीं हो सकता।',
    ],
    'caption'                => [
        'max_length'         => 'कैप्शन 500 अक्षरों से अधिक नहीं हो सकता।',
    ],

    // Category Validation
    'name_en'                => [
        'required'           => 'अंग्रेज़ी नाम आवश्यक है।',
        'min_length'         => 'अंग्रेज़ी नाम कम से कम 2 अक्षरों का होना चाहिए।',
    ],
    'name_hi'                => [
        'required'           => 'हिंदी नाम आवश्यक है।',
        'min_length'         => 'हिंदी नाम कम से कम 2 अक्षरों का होना चाहिए।',
    ],

    // Tag Validation
    'tag_name'               => [
        'required'           => 'टैग नाम आवश्यक है।',
        'min_length'         => 'टैग नाम कम से कम 2 अक्षरों का होना चाहिए।',
        'is_unique'          => 'यह टैग पहले से मौजूद है।',
    ],

    // Search Validation
    'q'                      => [
        'required'           => 'कृपया खोज शब्द दर्ज करें।',
        'min_length'         => 'खोज शब्द कम से कम 2 अक्षरों का होना चाहिए।',
    ],

    // Contact / Settings
    'setting_key'            => [
        'required'           => 'सेटिंग कुंजी आवश्यक है।',
    ],
    'setting_value'          => [
        'required'           => 'सेटिंग मान आवश्यक है।',
    ],

    // Rate Limiting / Throttle
    'too_many_requests'      => 'बहुत अधिक अनुरोध। कृपया बाद में पुनः प्रयास करें।',
    'rate_limit_exceeded'    => 'दर सीमा पार हो गई। कृपया {param} सेकंड प्रतीक्षा करें।',
];
