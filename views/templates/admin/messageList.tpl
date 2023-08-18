<table class="table table-striped table-bordered table-responsive">
    <thead>
        <tr class="chat-row">
            <th>Sender</th>
            <th>Sender's Name</th>
            <th>Latest Message Time</th>
            <th>Message</th>
            <th>Response</th>
        </tr>
    </thead>
   <tbody>
        {foreach from=$sendersAndMessages item=row}
            <tr class="chat-row" data-sender="{$row.from}">
                <td>{$row.from}</td>
                <td>{$row.customer_id}</td>
                <td>{$row.latest_message_time}</td>
                <td>{$row.message}</td>
                <td>
                {foreach from=$row.admin_responses item=response}
                    <p>{$response.response}</p>
                {/foreach}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
