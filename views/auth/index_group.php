<h1><?php echo $title;?></h1>
<p>Below is a list of the groups.</p>

<div id="infoMessage"><?php echo $message;?></div>

<table cellpadding=0 cellspacing=10>
	<tr>
		<th>Actions</th>
		<th>Name</th>
		<th>Description</th>
	</tr>
	<?php foreach ($groups as $group):?>
		<tr>
			<td><?php echo anchor("group/read/$group->id", 'View'), ' ', anchor("group/update/$group->id", 'Update'), ' ', anchor("group/delete/$group->id", 'Delete'); ?>
			<td><?php echo $group->name;?></td>
			<td><?php echo $group->description;?></td>
		</tr>
	<?php endforeach;?>
</table>

<p><a href="<?php echo site_url('group/create');?>">Create a new group</a></p>