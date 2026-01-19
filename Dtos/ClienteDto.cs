namespace ApiCobranzas.Dtos
{
    public class ClienteDto
    {
        public string Cedula { get; set; } = "";
        public string Nombres { get; set; } = "";
        public string? Email { get; set; }
        public string? Telefono { get; set; }
        public string? Direccion { get; set; }
        public bool Estado { get; set; } = true;
    }
}
