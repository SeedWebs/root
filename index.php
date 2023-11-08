<?php

global $wp;
wp_redirect(DEV_URL . '/' . $wp->request);
exit;
