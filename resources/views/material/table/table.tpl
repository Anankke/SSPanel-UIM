<table id="table_1" class="display responsive nowrap" style="width:100%">
  <thead>
    <tr>
      {foreach $table_config['total_column'] as $key => $value}
        <th class="{$key}">{$value}</th>
      {/foreach}
    </tr>
  </thead>
  <tfoot>
    <tr>
      {foreach $table_config['total_column'] as $key => $value}
        <th class="{$key}">{$value}</th>
      {/foreach}
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>
