<?php
 require __DIR__.'/../core/Dispatcher.php';
 Dispatcher::invoke([ 'controller_dir' => __DIR__.'/controller/'
                    ,'view_dir'       => __DIR__.'/views/'
                    ,'ignore_path'    => '\/sample']
                  );
