<table class="table" border="1">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Unit</th>
			<th>Price per Unit</th>
			<th>Stock</th>
		</tr>
	</thead>
	<tbody>
		<?php if (isset($zero_inventory)): ?>
			@foreach (Medical::zero() as $b)
				<tr>
					<td>
						{{$b->id}}
					</td>
					<td>
						{{$b->name}} - {{$b->brand}}
					</td>
					<td>
						{{$b->unit}}
					</td>
					<td>
						{{$b->price_unit}}
					</td>
					<td>
						{{$b->stock}}
					</td>
				</tr>
			@endforeach

		<?php else: ?>
			@foreach (Medical::all() as $b)
				<tr>
					<td>
						{{$b->id}}
					</td>
					<td>
						{{$b->name}} - {{$b->brand}}
					</td>
					<td>
						{{$b->unit}}
					</td>
					<td>
						{{$b->price_unit}}
					</td>
					<td>
						{{$b->stock}}
					</td>
				</tr>
			@endforeach

		<?php endif ?>
	</tbody>
</table>