<?php

\Route::any('/formbuilder/{ext}/{action}', ['uses'=>'Nifus\FormBuilder\Field@Index','as'=>'fb.action']);
