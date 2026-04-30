<a href="{{ route('sop.edit', $row->id) }}" class="btn btn-warning btn-sm mr-1">Edit</a>
<a href="{{ route('sop.show', $row->id) }}" class="btn btn-info btn-sm mr-1">Detail</a>
<form action="{{ route('sop.destroy', $row->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm"
            onclick="return confirm('Apakah Anda yakin ingin menghapus SOP ini?')">
        Hapus
    </button>
</form>
