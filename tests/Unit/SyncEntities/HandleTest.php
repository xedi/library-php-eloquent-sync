<?php

namespace Tests\Unit\SyncEntities;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Models\MailingList;
use Tests\Models\MailingListSubscriber;
use Tests\Unit\SyncEntities\TestCase;

class HandleTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     *
     * @dataProvider providesPositiveData
     */
    public function positive(callable $setup, callable $data, callable $tests)
    {
        $model = call_user_func($setup);
        $data = call_user_func($data);

        $results = $model->subscribers()->sync($data);

        call_user_func($tests, $model, $data, $results);
    }

    public function providesPositiveData()
    {
        return [
            'create subscribers' => $this->provideCreateScenario(),
            'update subscribers' => $this->provideUpdateScenario(),
            'delete subscribers' => $this->provideDeleteScenario(),
            'replaces subscribers' => $this->provideReplaceScenario(),
        ];
    }

    protected function provideCreateScenario(): array
    {
        return [
            function () {
                return factory(MailingList::class)
                    ->create();
            },
            function () {
                return factory(MailingListSubscriber::class, 2)
                    ->raw();
            },
            function ($model, $data, $actual_results) {
                $model->unsetRelation('subscribers');
                $subscribers = MailingListSubscriber::all();
                $expected_results = [
                    'created' => $subscribers->pluck('id')->toArray(),
                    'updated' => [],
                    'deleted' => [],
                ];

                $this->assertNotEmpty($model->subscribers);
                $this->assertEquals($subscribers, $model->subscribers);

                $this->assertEquals($expected_results, $actual_results);
            }
        ];
    }

    protected function provideUpdateScenario()
    {
        $this->setUpFaker();

        return [
            function () {
                return tap(factory(MailingList::class)->create(), function ($mailing_list) {
                    factory(MailingListSubscriber::class, 2)
                        ->create([ 'mailing_list_id' => $mailing_list->id ]);
                });
            },
            function () {
                return MailingListSubscriber::all()
                    ->each(function ($subscriber) {
                        $subscriber->email_address = $this->faker->email;
                    })
                    ->toArray();
            },
            function ($model, $data, $actual_results) {
                $model->unsetRelation('subscribers');
                $subscribers = MailingListSubscriber::all();
                $expected_results = [
                    'created' => [],
                    'updated' => $subscribers->pluck('id')->toArray(),
                    'deleted' => [],
                ];

                $this->assertNotEmpty($model->subscribers);
                $this->assertEquals($subscribers, $model->subscribers);

                $this->assertEquals($expected_results, $actual_results);
            }
        ];
    }

    protected function provideDeleteScenario()
    {
        return [
            function () {
                return tap(factory(MailingList::class)->create(), function ($mailing_list) {
                    factory(MailingListSubscriber::class, 2)
                        ->create([ 'mailing_list_id' => $mailing_list->id ]);
                });
            },
            function () {
                return [];
            },
            function ($model, $data, $actual_results) {
                $model->unsetRelation('subscribers');
                $expected_results = [
                    'created' => [],
                    'updated' => [],
                    'deleted' => [1,2],
                ];

                $this->assertEmpty($model->subscribers);

                $this->assertEquals($expected_results, $actual_results);
            }
        ];
    }

    protected function provideReplaceScenario()
    {
        return [
            function () {
                return tap(factory(MailingList::class)->create(), function ($mailing_list) {
                    factory(MailingListSubscriber::class, 2)
                        ->create([ 'mailing_list_id' => $mailing_list->id ]);
                });
            },
            function () {
                return factory(MailingListSubscriber::class, 2)
                    ->raw();
            },
            function ($model, $data, $actual_results) {
                $model->unsetRelation('subscribers');
                $subscribers = MailingListSubscriber::all();
                $expected_results = [
                    'created' => $subscribers->pluck('id')->toArray(),
                    'updated' => [],
                    'deleted' => [1,2],
                ];

                $this->assertNotEmpty($model->subscribers);
                $this->assertEquals($subscribers, $model->subscribers);

                $this->assertEquals($expected_results, $actual_results);
            }
        ];
    }
}
