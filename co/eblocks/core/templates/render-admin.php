<?php
class STB_SuperAdminController {

    static function render() {
        $appIconLarge = plugins_url('', dirname(dirname( __FILE__ )) . '../') . '/assets/super-admin/img/wp-icon-100x100.png';
        
        $isSSL = isset($_SERVER['HTTPS']) ? true : false;
        $isCurl = function_exists('curl_version');
        $connection = get_option(STB_APP_CURRENT_CONNECTION_KEY_SLUG, '');
        $isLocalhost = (substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1') ? true : false;
        ?>  
            <div id="main">
                <div class="container-fluid">
                    <?php
                        if (!$isSSL) {
                            ?>
                            <div class="app-requirement">
                                <?php self::renderSSlRequired(); ?>
                            </div>
                            <?php
                        }
                        if (!$isCurl) {
                            ?>
                            <div class="app-requirement">
                                <?php self::renderRequiredCurl(); ?>
                            </div>
                            <?php
                        }
                        if ($isLocalhost) {
                            ?>
                            <div class="app-requirement">
                                <?php self::renderCriticalBanner('Local development', 'Currently, you are using a local development environment, you will not be able to connect to your Shopify store from localhost.'); ?>
                            </div>
                            <?php
                        }
                    ?>
                    <div class="row">
                        <div class="col">
                            <?php if (is_array($connection)): ?>
                                <?php self::renderSuccessBanner('Success', 'You have successfully connected your WP blog to your Shopify store <a href="https://'. $connection['shopDomain'] .'" target="_blank">' . $connection['shopName'] . '</a>! Go to the app and complete your Shopify blog setup.'); ?>
                            <?php endif;?>
                            <div class="eb-panel">
                                <h3 class="eb-panel-title">Connect to your Shopify store (Access Key)</h3>
                                <div class="eb-panel-content">                                    
                                    <p>You can obtain an Access Key right after you have installed the StoryBook - WordPress Blog Connector (Shopify App).</p>
                                    <br />
                                    <p>Add your Access Key</p>
                                    <input id="accessKey" type="text" value="<?php echo get_option(STB_APP_ACCES_KEY_SLUG, ''); ?>" style="margin: 10px 0px; width: 100%; outline: none; margin-bottom: 15px;" />
                                    <button id="connect_btn" style="font-size: 15px;" type="button" class="btn btn-primary">Connect</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="eb-panel">
                                <h3 class="eb-panel-title">About</h3>
                                <div class="eb-panel-content">
                                    <p>StoryBook Connector helps you connect your WordPress blog to your Shopify store. You will be able to display your posts and much more right on your Shopify domain.</p>
                                    <p>First, you will need to install the <a target="_blank" href="<?= STB_APP_INSTALL_URL; ?>">StoryBook - WP Blog Connector (Shopify App)</a> within your Shopify store.</p>
                                </div>
                            </div>
                        </div>                        
                    </div>
         
                </div>
            </div>
        <?php
    }

    // has required php version
    static function hasRequiredPhpVersion() {
        $phpVersion = explode('.', phpversion());
        $isRequiredPhpVersion = false;
        if (isset($phpVersion[0]) && $phpVersion[0] >= 5 && isset($phpVersion[1]) && $phpVersion[1] >= 5) {
            $isRequiredPhpVersion = true;
        }
        return $isRequiredPhpVersion;
    }

    // required PHP version
    static function renderRequiredPhp() {
        self::renderWarningBanner('PHP version required', 'PHP version 5.5 or greater is required.');
    }

    // required cURL
    static function renderRequiredCurl() {
        self::renderWarningBanner('cURL is required!', 'In order to communicate with <a href="STB_APP_INSTALL_URL" target="_blank">StoryBook (Shopify App)</a>, PHP cURL extension is required. Please contact your hosting provider and ask to enable cURL.');
    }

    static function renderSSlRequired() {
        $currentHost = $_SERVER['HTTP_HOST'];
        self::renderWarningBanner('SSL is recommended!', 'Your blog ' . $currentHost . ' uses HTTP protocol while all Shopify stores, are being served over HTTPS protocol, this will prevent images from your ' . $currentHost . ' to be displayed within your Shopify blog.');
    }
    
    static function renderWarningBanner($title, $content) {
        ?>
            <div style="margin-bottom: 20px; --top-bar-background:#00848e; --top-bar-color:#f9fafb; --top-bar-background-darker:#006d74; --top-bar-background-lighter:#1d9ba4;">
                <div class="Polaris-Banner Polaris-Banner--statusWarning Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner3Heading" aria-describedby="Banner3Content">
                    <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorYellowDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true">
                        <g fill-rule="evenodd">
                            <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
                            <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m0-13a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V6a1 1 0 0 0-1-1m0 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2"></path>
                        </g>
                        </svg></span></div>
                    <div>
                    <div class="Polaris-Banner__Heading" id="Banner3Heading">
                        <p class="Polaris-Heading"><?php echo $title; ?></p>
                    </div>
                    <div class="Polaris-Banner__Content" id="Banner3Content">
                        <ul class="Polaris-List">
                            <li class="Polaris-List__Item"><?php echo $content; ?></li>
                        </ul>
                    </div>
                    </div>
                </div>
            </div>      
        <?php
    }

    static function renderSuccessBanner($title, $content) {
        ?>
        <div style="margin-bottom: 20px; --top-bar-background:#00848e; --top-bar-color:#f9fafb; --top-bar-background-lighter:#1d9ba4;">
            <div class="Polaris-Banner Polaris-Banner--statusSuccess Polaris-Banner--hasDismiss Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="Banner3Heading" aria-describedby="Banner3Content">

                <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorGreenDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true">
                    <g fill-rule="evenodd">
                        <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
                        <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m2.293-10.707L9 10.586 7.707 9.293a1 1 0 1 0-1.414 1.414l2 2a.997.997 0 0 0 1.414 0l4-4a1 1 0 1 0-1.414-1.414"></path>
                    </g>
                    </svg></span></div>
                <div>
                <div class="Polaris-Banner__Heading" id="Banner3Heading">
                    <p class="Polaris-Heading"><?php echo $title; ?></p>
                </div>
                <div class="Polaris-Banner__Content" id="Banner3Content">
                        <ul class="Polaris-List">
                            <li class="Polaris-List__Item"><?php echo $content; ?></li>
                        </ul>
                </div>
                </div>
            </div>
        </div>        
        <?php
    }

    static function renderCriticalBanner($title, $content) {
        ?>
        <div style="margin-bottom: 20px; --top-bar-background:#00848e; --top-bar-color:#f9fafb; --top-bar-background-lighter:#1d9ba4;">
            <div class="Polaris-Banner Polaris-Banner--statusCritical Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner4Heading" aria-describedby="Banner4Content">
                <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorRedDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true">
                    <g fill-rule="evenodd">
                        <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
                        <path d="M2 10c0-1.846.635-3.543 1.688-4.897l11.209 11.209A7.954 7.954 0 0 1 10 18c-4.411 0-8-3.589-8-8m14.312 4.897L5.103 3.688A7.954 7.954 0 0 1 10 2c4.411 0 8 3.589 8 8a7.952 7.952 0 0 1-1.688 4.897M0 10c0 5.514 4.486 10 10 10s10-4.486 10-10S15.514 0 10 0 0 4.486 0 10"></path>
                    </g>
                    </svg></span></div>
                <div>
                <div class="Polaris-Banner__Heading" id="Banner4Heading">
                    <p class="Polaris-Heading"><?php echo $title; ?></p>
                </div>
                <div class="Polaris-Banner__Content" id="Banner4Content">
                    <p><?php echo $content; ?></p>
                </div>
                </div>
            </div>
        </div>        
        <?php        
    }
}
?>