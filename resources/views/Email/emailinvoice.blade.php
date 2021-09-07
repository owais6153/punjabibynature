<div style="max-width: 800px;margin: auto;padding: 30px;border: 1px solid #eee;box-shadow: 0 0 10px rgba(0, 0, 0, .15);font-size: 16px;line-height: 24px;font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;color: #555;">
    <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
        <tr>
            <td colspan="5" style="padding: 5px;vertical-align: top;">
                <table style="width: 100%;line-height: inherit;text-align: left;">
                    <tr>
                        <td style="padding: 5px;vertical-align: top;padding-bottom: 20px;font-size: 45px;line-height: 45px;color: #333;">
                            <img src='{!! asset("storage/app/public/images/about/".$logo) !!}' style="width:100%; max-width:100px;">
                        </td>
                        
                        <td style="padding: 5px;vertical-align: top;text-align: right;padding-bottom: 20px;">
                            Invoice # {{$getusers->order_number}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
            <td colspan="5" style="padding: 5px;vertical-align: top;">
                <table style="width: 100%;line-height: inherit;text-align: left;">
                    <tr>
                        <td style="padding: 5px;vertical-align: top;padding-bottom: 40px;">
                            {{$getusers["users"]->name}}<br>
                            {{$getusers->address}}<br>
                            {{$getusers["users"]->email}}<br>
                            {{$getusers["users"]->mobile}}
                        </td>
                        
                        <td style="padding: 5px;vertical-align: top;text-align: right;padding-bottom: 40px;">
                            @if ($getusers['order_notes'] !="")

                                <strong>Order Note:</strong><br>

                                {{$getusers['order_notes']}}

                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
          <td style="padding: 5px;vertical-align: top;background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">
                #
            </td>
            <td style="padding: 5px;vertical-align: top;background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">
                Item
            </td>
            <td style="padding: 5px;vertical-align: top;background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">
                Unit cost
            </td>
            <td style="padding: 5px;vertical-align: top;background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">
                Qty
            </td>
            <td style="padding: 5px;vertical-align: top;text-align: right;background: #eee;border-bottom: 1px solid #ddd;font-weight: bold;">
                Price
            </td>
        </tr>
        <?php

        $i=1;

        foreach ($getorders as $orders) {
            $data[] = array(
                "total_price" => $orders['qty'] * $orders['total_price'],
            );
        ?>
        <tr>
            <td style="padding: 5px;vertical-align: top;border-bottom: 1px solid #eee;">
                {{$i}}
            </td>
            <td style="padding: 5px;vertical-align: top;border-bottom: 1px solid #eee;">
                {{$orders['item_name']}}
                <br>
                @if (isset($orders['addons']) && $orders['addons'] != "")
                  @foreach ($orders['addons'] as $addons)

                  <b>{{$addons['name']}}</b> : {{$currency}}{{number_format($addons['price'], 2)}}<br>

                  @endforeach
                @endif

                @if ($orders['item_notes'] != "")

                    <b>Item Notes</b> : {{$orders['item_notes']}}

                @endif
            </td>
            <td style="padding: 5px;vertical-align: top;border-bottom: 1px solid #eee;">
                {{$currency}}{{number_format($orders['variation_price'], 2)}}
            </td>
            <td style="padding: 5px;vertical-align: top;border-bottom: 1px solid #eee;">
                {{$orders['qty']}}
            </td>            
            <td style="padding: 5px;vertical-align: top;text-align: right;border-bottom: 1px solid #eee;">
                {{$currency}}{{number_format($orders['total_price'], 2)}}
            </td>
        </tr>
        <?php

        $i++;

        }
        $order_total = array_sum(array_column(@$data, 'total_price'));
        ?>

        <tr>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            
            <td style="padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold;">
               <strong>Subtotal</strong> : {{$currency}}{{number_format($order_total, 2)}}
            </td>
        </tr>

        <tr>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            
            <td style="padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold;">
               Delivery Charge : {{$currency}}{{number_format($getusers->delivery_charge, 2)}}
            </td>
        </tr>

        @if ($getusers->discount_amount != 0)
        <tr>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            
            <td style="padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold;">
               <strong>Discount</strong> ({{$getusers->promocode}}) : - {{$currency}}{{number_format($getusers->discount_amount, 2)}}
            </td>
        </tr>
        @endif
        
        <tr>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            <td style="padding: 5px;vertical-align: top;"></td>
            
            <td style="padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold;">
               Total : {{$currency}}{{number_format($getusers->order_total, 2)}}
            </td>
        </tr>
    </table>
</div>