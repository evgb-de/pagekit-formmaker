<?php

namespace Bixie\Formmaker\Plugin;

use Pagekit\Application as App;
use Pagekit\Content\Event\ContentEvent;
use Pagekit\Event\EventSubscriberInterface;
use Bixie\Formmaker\Model\Form;

class FormmakerPlugin implements EventSubscriberInterface
{

    /**
     * Content plugins callback.
     *
     * @param ContentEvent $event
     */
    public function onContentPlugins(ContentEvent $event)
    {
        $event->addPlugin('formmaker', [$this, 'applyPlugin']);
    }

    /**
     * Defines the plugins callback.
     *
     * @param  array $options
     * @return string
     */
    public function applyPlugin(array $options)
    {
        if (!isset($options['id'])) {
            return;
        }

		$user = App::user();
		$formmaker = App::module('bixie/formmaker');

		if (!$form = Form::where(['id = ?'], [$options['id']])->where(function ($query) use ($user) {
			if (!$user->isAdministrator()) $query->where('status = 1');
		})->related('fields')->first()
		) {
			return 'Form not found';
		}

		$form->set('hide_title', !empty($options['hide_title']) ? true : false);

		$form->prepareView();

		App::on('view.data', function ($event, $data) use ($form, $formmaker) {
			$data->add('$formmaker', [
				'config' => $formmaker->publicConfig(),
				'formitem' => $form,
				'fields' => array_values($form->fields)
			]);
		});

		App::on('view.styles', function ($event, $styles) use ($formmaker) {
			$formmaker->typeStyles($styles);
		});

		return App::view('bixie/formmaker/form.php');
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'content.plugins' => ['onContentPlugins', 25],
        ];
    }
}
