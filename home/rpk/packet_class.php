<?php
// Herzlich Willkommen. Das ist "Packet Class PW".
// Bei Desmond Hume
class ReadPacket
{
	public $data, $pos;
	
	function __construct($obj = null)
	{
		$this -> data = $obj -> response;
	}
	
	public function ReadBytes($length)
	{
		$value = substr($this -> data, $this -> pos, $length);
		$this -> pos += $length;
		
		return $value;
	}
	
	public function ReadUByte()
	{
		$value = unpack("C", substr($this -> data, $this -> pos, 1));
		$this -> pos++;
		
		return $value[1];
	}

	public function ReadFloat()
	{
		$value = unpack("f", strrev(substr($this -> data, $this -> pos, 4)));
		$this -> pos += 4;
		
		return $value[1];
	}
	
	public function ReadUInt32()
	{
		$value = unpack("N", substr($this -> data, $this -> pos, 4));
		$this -> pos += 4;
		
		return $value[1];
	}
	
	public function ReadUInt16()
	{
		$value = unpack("n", substr($this -> data, $this -> pos, 2));
		$this -> pos += 2;
		
		return $value[1];
	}
	
	
	public function ReadOctets()
	{
		$length = $this -> ReadCUInt32();
	
		$value = unpack("H*", substr($this -> data, $this -> pos, $length));
		$this -> pos += $length;
		
		return $value[1];
	}
	
	public function ReadUString()
	{
		$length = $this -> ReadCUInt32();
	
		$value = iconv("UTF-16", "UTF-8", substr($this -> data, $this -> pos, $length)); // LE?
		$this -> pos += $length;
		
		return $value;
	}
	
	public function ReadPacketInfo()
	{
		$packetinfo['Opcode'] = $this -> ReadCUInt32();
		$packetinfo['Length'] = $this -> ReadCUInt32();
		return $packetinfo;
	}
	
	public function Seek($value)
	{
		$this -> pos += $value;
	}
	
	public function ReadCUInt32()
	{
		$value = unpack("C", substr($this -> data, $this -> pos, 1));
		$value = $value[1];
		$this -> pos++;
		
		switch($value & 0xE0)
		{
			case 0xE0:
				$value = unpack("N", substr($this -> data, $this -> pos, 4));
				$value = $value[1];
				$this -> pos += 4;
				break;
			case 0xC0:
				$value = unpack("N", substr($this -> data, $this -> pos - 1, 4));
				$value = $value[1] & 0x1FFFFFFF;
				$this -> pos += 3;
				break;
			case 0x80:
			case 0xA0:
				$value = unpack("n", substr($this -> data, $this -> pos - 1, 2));
				$value = $value[1] & 0x3FFF;
				$this -> pos++;
				break;
		}
		
		return $value;
	}
}

class WritePacket
{
	public $request, $response;
	
	public function WriteBytes($value)
	{
		$this -> request .= $value;
	}
	
	public function WriteUByte($value)
	{
		$this -> request .= pack("C", $value);
	}
	
	public function WriteFloat($value)
	{
		$this -> request .= strrev(pack("f", $value));
	}
	
	public function WriteUInt32($value)
	{
		$this -> request .= pack("N", $value);
	}
	
	public function WriteUInt16($value)
	{
		$this -> request .= pack("n", $value);
	}
	
	public function WriteOctets($value)
	{
		if (ctype_xdigit($value))
			$value = pack("H*", $value);
			
		$this -> request .= $this -> CUInt(strlen($value));
		$this -> request .= $value;
	}
	
	public function WriteUString($value, $coding = "UTF-16LE")
	{
		$value = iconv("UTF-8", $coding, $value);
		$this -> request .= $this -> CUInt(strlen($value));
		$this -> request .= $value;
	}
	
	public function Pack($value)
	{
		$this -> request = $this -> CUInt($value) . $this -> CUInt(strlen($this -> request)) . $this -> request;
	}
	
	public function Unmarshal()
	{
		return $this -> CUInt(strlen($this -> request)) . $this -> request;
	}
	
	public function Send($address, $port)
	{
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		
		if (socket_connect($socket, $address, $port))
		{
			socket_set_block($socket);

			$send = socket_send($socket, $this -> request, 131072, 0);
			$recv = socket_recv($socket, $this -> response, 131072, 0); 
			socket_set_nonblock($socket);
			socket_close($socket);
			
			return true;
		}
		else
			return false;
	}
	
	public function WriteCUInt32($value)
	{
			$this -> request .= $this -> CUInt($value);
	}
	
	private function CUInt($value)
	{
		if ($value <= 0x7F)
			return pack("C", $value);
		else if ($value <= 0x3FFF)
			return pack("n", ($value | 0x8000));
		else if ($value <= 0x1FFFFFFF)
			return pack("N", ($value | 0xC0000000));
		else
			return pack("C", 0xE0) . pack("N", $value);
	}
}

?>